<?php

namespace App\Observers;

use App\Models\FilledLetter;
use App\Models\LetterQueue;
use App\Models\ServiceSchedule;
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
                // Cari jadwal pelayanan yang aktif
                $serviceSchedule = ServiceSchedule::where('is_active', true)->first();

                if (!$serviceSchedule) {
                    // Jika tidak ada jadwal pelayanan, gunakan default (1 hari dari sekarang)
                    $scheduledDate = now()->addDay();
                } else {
                    // Cari antrian terakhir untuk menentukan jadwal berikutnya
                    $lastQueue = LetterQueue::where('status', 'waiting')
                        ->orderBy('scheduled_date', 'desc')
                        ->first();

                    if (!$lastQueue) {
                        // Jika belum ada antrian, jadwalkan di awal jam pelayanan hari ini atau besok
                        $today = Carbon::today();
                        $now = Carbon::now();
                        $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($today);

                        // Jika sekarang sudah melewati jam mulai hari ini, jadwalkan besok
                        if ($now->gt($startTime)) {
                            $tomorrow = Carbon::tomorrow();
                            $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($tomorrow);
                        }

                        $scheduledDate = $startTime;
                    } else {
                        // Jadwalkan sesuai waktu proses setelah antrian terakhir
                        $scheduledDate = Carbon::parse($lastQueue->scheduled_date)->addMinutes($serviceSchedule->processing_time);

                        // Pastikan masih dalam jam pelayanan
                        $scheduleDate = $scheduledDate->format('Y-m-d');
                        $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($scheduleDate);
                        $endTime = Carbon::parse($serviceSchedule->end_time)->setDateFrom($scheduleDate);

                        // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
                        if ($scheduledDate->gt($endTime)) {
                            $nextDay = Carbon::parse($scheduleDate)->addDay();
                            $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextDay);
                        }

                        // Jika jadwal sebelum jam mulai pelayanan, pindahkan ke jam mulai pelayanan
                        if ($scheduledDate->lt($startTime)) {
                            $scheduledDate = $startTime;
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
