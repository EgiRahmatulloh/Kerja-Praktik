<?php

namespace App\Observers;

use App\Models\FilledLetter;
use App\Models\LetterQueue;
use App\Models\ServiceSchedule;
use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FilledLetterObserver
{
    /**
     * Handle the FilledLetter "saving" event.
     *
     * @param  \App\Models\FilledLetter  $filledLetter
     * @return void
     */
    public function saving(FilledLetter $filledLetter)
    {
        // Jika status diubah menjadi pending atau rejected, hapus dari antrian
        if (
            $filledLetter->isDirty('status') &&
            ($filledLetter->status === 'pending' || $filledLetter->status === 'rejected')
        ) {
            // Cari dan hapus semua antrian yang terkait dengan surat ini
            $queues = LetterQueue::where('filled_letter_id', $filledLetter->id)->get();
            if ($queues->isNotEmpty()) {
                foreach ($queues as $queue) {
                    Log::info("Menghapus antrian #{$queue->id} karena status surat #{$filledLetter->id} diubah menjadi {$filledLetter->status}");
                    $queue->delete();
                }
            }
        }

        // Jika status diubah menjadi approved, tambahkan ke antrian
        if (
            $filledLetter->isDirty('status') &&
            $filledLetter->status === 'approved'
        ) {
            // Pastikan surat belum ada di antrian
            $existingQueue = LetterQueue::where('filled_letter_id', $filledLetter->id)->first();

            if (!$existingQueue) {
                // Cari jadwal pelayanan yang aktif dan tidak dijeda
                $serviceSchedule = ServiceSchedule::where('is_active', true)
                    ->where('is_paused', false)
                    ->first();
                
                // Jika tidak ada jadwal aktif yang tidak dijeda, cari jadwal aktif lainnya
                if (!$serviceSchedule) {
                    $serviceSchedule = ServiceSchedule::where('is_active', true)->first();
                }

                if (!$serviceSchedule) {
                    // Jika tidak ada jadwal pelayanan, gunakan default (2 hari dari sekarang)
                    $holidayService = new HolidayService();
                    $twoDaysLater = Carbon::today()->addDays(2);
                    $nextWorkingDay = $holidayService->isWorkingDay($twoDaysLater) ? $twoDaysLater : $holidayService->getNextWorkingDay($twoDaysLater);
                    $scheduledDate = $nextWorkingDay->setTime(8, 0); // Set jam 8 pagi sebagai default
                } else {
                    // Cari antrian terakhir untuk menentukan jadwal berikutnya
                    $lastQueue = LetterQueue::where('status', 'waiting')
                        ->orderBy('scheduled_date', 'desc')
                        ->first();

                    if (!$lastQueue) {
                        // Jika belum ada antrian, jadwalkan di awal jam pelayanan 2 hari kerja berikutnya
                        $holidayService = new HolidayService();
                        $twoDaysLater = Carbon::today()->addDays(2);
                        $nextWorkingDay = $holidayService->isWorkingDay($twoDaysLater) ? $twoDaysLater : $holidayService->getNextWorkingDay($twoDaysLater);
                        $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
                    } else {
                        // Jadwalkan sesuai waktu proses setelah antrian terakhir
                        $scheduledDate = Carbon::parse($lastQueue->scheduled_date)->addMinutes($serviceSchedule->processing_time);
                        
                        // Pastikan minimal H+2
                        $minDate = Carbon::today()->addDays(2)->startOfDay();
                        if ($scheduledDate->lt($minDate)) {
                            $holidayService = new HolidayService();
                            $twoDaysLater = Carbon::today()->addDays(2);
                            $nextWorkingDay = $holidayService->isWorkingDay($twoDaysLater) ? $twoDaysLater : $holidayService->getNextWorkingDay($twoDaysLater);
                            $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
                        } else {
                            // Pastikan masih dalam jam pelayanan
                            $scheduleDate = $scheduledDate->format('Y-m-d');
                            $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($scheduleDate);
                            $endTime = Carbon::parse($serviceSchedule->end_time)->setDateFrom($scheduleDate);

                            // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
                            if ($scheduledDate->gt($endTime)) {
                                $holidayService = new HolidayService();
                                $nextWorkingDay = $holidayService->getNextWorkingDay(Carbon::parse($scheduleDate));
                                $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
                            }

                            // Jika jadwal sebelum jam mulai pelayanan, pindahkan ke jam mulai pelayanan
                            if ($scheduledDate->lt($startTime)) {
                                $scheduledDate = $startTime;
                            }
                            
                            // Pastikan tanggal yang dijadwalkan bukan hari libur
                            $holidayService = new HolidayService();
                            if ($holidayService->isHoliday($scheduledDate)) {
                                $nextWorkingDay = $holidayService->getNextWorkingDay($scheduledDate);
                                $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
                            }
                        }
                    }
                }

                // Buat antrian baru
                LetterQueue::create([
                    'filled_letter_id' => $filledLetter->id,
                    'scheduled_date' => $scheduledDate,
                    'status' => 'waiting'
                ]);

                Log::info("Menambahkan surat #{$filledLetter->id} ke antrian karena status diubah menjadi approved");
            }
        }
    }
}
