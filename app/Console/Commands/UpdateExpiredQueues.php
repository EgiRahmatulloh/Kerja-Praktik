<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LetterQueue;
use App\Models\ServiceSchedule;
use App\Services\HolidayService;
use Carbon\Carbon;

class UpdateExpiredQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queues:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired letter queues to the next available service day';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to update expired letter queues...');

        // Ambil jadwal pelayanan yang aktif
        $serviceSchedule = ServiceSchedule::where('is_active', true)->first();

        if (!$serviceSchedule) {
            $this->error('No active service schedule found!');
            return 1;
        }

        // Ambil semua antrian dengan status waiting yang tanggalnya sudah lewat
        $now = Carbon::now();
        $expiredQueues = LetterQueue::where('status', 'waiting')
            ->where('scheduled_date', '<', $now)
            ->orderBy('id', 'asc') // Urutkan berdasarkan ID untuk memastikan urutan sesuai dengan urutan antrian
            ->get();

        // Hapus antrian duplikat berdasarkan filled_letter_id
        $uniqueQueues = collect();
        $seenLetterIds = [];

        foreach ($expiredQueues as $queue) {
            if (!in_array($queue->filled_letter_id, $seenLetterIds)) {
                $uniqueQueues->push($queue);
                $seenLetterIds[] = $queue->filled_letter_id;
            }
        }

        $expiredQueues = $uniqueQueues;

        if ($expiredQueues->isEmpty()) {
            $this->info('No expired queues found.');
            return 0;
        }

        $this->info('Found ' . $expiredQueues->count() . ' expired queues.');

        // Tentukan tanggal layanan berikutnya (hari ini atau besok)
        $nextServiceDate = $this->getNextServiceDate($serviceSchedule);

        // Hapus antrian duplikat dari database
        $this->cleanupDuplicateQueues();

        // Proses setiap antrian yang sudah lewat
        $scheduledTime = Carbon::parse($nextServiceDate);

        foreach ($expiredQueues as $queue) {
            // Pastikan masih dalam jam pelayanan
            $scheduleDate = $scheduledTime->format('Y-m-d');
            $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($scheduleDate);
            $endTime = Carbon::parse($serviceSchedule->end_time)->setDateFrom($scheduleDate);

            // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
            if ($scheduledTime->gt($endTime)) {
                $holidayService = new HolidayService();
                $nextWorkingDay = $holidayService->getNextWorkingDay(Carbon::parse($scheduleDate));
                $scheduledTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
            }
            
            // Pastikan tanggal yang dijadwalkan bukan hari libur
            $holidayService = new HolidayService();
            if ($holidayService->isHoliday($scheduledTime)) {
                $nextWorkingDay = $holidayService->getNextWorkingDay($scheduledTime);
                $scheduledTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
            }

            // Hitung waktu jadwal baru berdasarkan urutan
            $processingTime = $serviceSchedule->processing_time;

            // Update jadwal antrian
            $queue->update([
                'scheduled_date' => $scheduledTime,
            ]);

            $this->info("Updated queue #{$queue->id} to {$scheduledTime}");

            // Siapkan jadwal untuk antrian berikutnya
            $scheduledTime = $scheduledTime->copy()->addMinutes($processingTime);
        }

        $this->info('All expired queues have been updated successfully.');
        return 0;
    }

    /**
     * Mendapatkan tanggal layanan berikutnya berdasarkan jadwal pelayanan
     */
    private function getNextServiceDate($serviceSchedule)
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // Jam mulai dan selesai pelayanan hari ini
        $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($today);
        $endTime = Carbon::parse($serviceSchedule->end_time)->setDateFrom($today);

        // Jika sekarang masih dalam jam pelayanan, gunakan waktu sekarang
        if ($now->gte($startTime) && $now->lte($endTime)) {
            return $now->format('Y-m-d H:i:s');
        }

        // Jika sekarang sebelum jam mulai pelayanan hari ini, gunakan jam mulai hari ini
        if ($now->lt($startTime)) {
            return $startTime->format('Y-m-d H:i:s');
        }

        // Jika sekarang setelah jam selesai pelayanan, gunakan jam mulai besok
        $tomorrow = Carbon::tomorrow();
        return Carbon::parse($serviceSchedule->start_time)->setDateFrom($tomorrow)->format('Y-m-d H:i:s');
    }

    /**
     * Membersihkan antrian duplikat dari database
     * Hanya menyimpan satu antrian untuk setiap surat yang diisi
     */
    private function cleanupDuplicateQueues()
    {
        $this->info('Cleaning up duplicate queues...');

        // Ambil semua antrian dengan status waiting
        $waitingQueues = LetterQueue::where('status', 'waiting')->get();

        // Kelompokkan berdasarkan filled_letter_id
        $groupedQueues = $waitingQueues->groupBy('filled_letter_id');

        $deletedCount = 0;

        // Untuk setiap kelompok, simpan hanya antrian dengan ID terkecil
        foreach ($groupedQueues as $filledLetterId => $queues) {
            if ($queues->count() > 1) {
                // Urutkan berdasarkan ID
                $sortedQueues = $queues->sortBy('id');

                // Simpan yang pertama (ID terkecil)
                $keepQueue = $sortedQueues->first();

                // Hapus sisanya
                foreach ($sortedQueues as $queue) {
                    if ($queue->id != $keepQueue->id) {
                        $queue->delete();
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} duplicate queues.");
        } else {
            $this->info('No duplicate queues found.');
        }
    }
}
