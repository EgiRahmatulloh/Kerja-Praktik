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
                // Tentukan admin yang akan menangani berdasarkan template surat
                $templateOwner = $filledLetter->letterType->templateSurat->owner;
                
                // Cari jadwal pelayanan yang aktif dan tidak dijeda berdasarkan pemilik template
                $serviceSchedule = ServiceSchedule::where('user_id', $templateOwner->id)
                    ->where('is_active', true)
                    ->where('is_paused', false)
                    ->first();
                
                // Jika tidak ada jadwal aktif yang tidak dijeda, cari jadwal aktif lainnya dari admin yang sama
                if (!$serviceSchedule) {
                    $serviceSchedule = ServiceSchedule::where('user_id', $templateOwner->id)
                        ->where('is_active', true)
                        ->first();
                }
                
                // Jika admin tersebut tidak memiliki jadwal, cari jadwal dari admin lain
                if (!$serviceSchedule) {
                    $serviceSchedule = ServiceSchedule::where('is_active', true)
                        ->where('is_paused', false)
                        ->first();
                }
                
                // Jika masih tidak ada, cari jadwal aktif manapun
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
                    // Cari antrian terakhir untuk menentukan jadwal berikutnya dari admin yang sama
                    $lastQueue = LetterQueue::where('status', 'waiting')
                        ->where('service_schedule_id', $serviceSchedule->id)
                        ->orderBy('scheduled_date', 'desc')
                        ->first();
                    
                    // Jika tidak ada antrian dari admin yang sama, cari antrian terakhir secara umum
                    if (!$lastQueue) {
                        $lastQueue = LetterQueue::where('status', 'waiting')
                            ->whereHas('serviceSchedule', function($q) use ($serviceSchedule) {
                                $q->where('user_id', $serviceSchedule->user_id);
                            })
                            ->orderBy('scheduled_date', 'desc')
                            ->first();
                    }

                    if (!$lastQueue) {
                        // Jika belum ada antrian, jadwalkan di awal jam pelayanan 2 hari kerja berikutnya
                        $holidayService = new HolidayService();
                        $twoDaysLater = Carbon::today()->addDays(2);
                        $nextWorkingDay = $holidayService->isWorkingDay($twoDaysLater) ? $twoDaysLater : $holidayService->getNextWorkingDay($twoDaysLater);
                        $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
                        
                        // Cek apakah jadwal berada dalam jam istirahat
                        $scheduledDate = $this->adjustForBreakTime($scheduledDate, $serviceSchedule);
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

                            // Cek apakah jadwal berada dalam jam istirahat
                            $scheduledDate = $this->adjustForBreakTime($scheduledDate, $serviceSchedule);
                            
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
                    'service_schedule_id' => $serviceSchedule ? $serviceSchedule->id : null,
                    'scheduled_date' => $scheduledDate,
                    'status' => 'waiting'
                ]);

                Log::info("Menambahkan surat #{$filledLetter->id} ke antrian karena status diubah menjadi approved");
            }
        }
    }

    /**
     * Menyesuaikan jadwal jika berada dalam jam istirahat
     *
     * @param Carbon $scheduledDate
     * @param ServiceSchedule $serviceSchedule
     * @return Carbon
     */
    private function adjustForBreakTime(Carbon $scheduledDate, ServiceSchedule $serviceSchedule)
    {
        // Jika tidak ada jam istirahat yang diset, return jadwal asli
        if (!$serviceSchedule->break_start_time || !$serviceSchedule->break_end_time) {
            return $scheduledDate;
        }

        $scheduleDate = $scheduledDate->format('Y-m-d');
        $breakStartTime = Carbon::parse($serviceSchedule->break_start_time)->setDateFrom($scheduleDate);
        $breakEndTime = Carbon::parse($serviceSchedule->break_end_time)->setDateFrom($scheduleDate);
        $endTime = Carbon::parse($serviceSchedule->end_time)->setDateFrom($scheduleDate);

        // Jika jadwal berada dalam jam istirahat, pindahkan ke setelah jam istirahat
        if ($scheduledDate->between($breakStartTime, $breakEndTime)) {
            $scheduledDate = $breakEndTime->copy();
            
            // Jika setelah jam istirahat melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
            if ($scheduledDate->gt($endTime)) {
                $holidayService = new HolidayService();
                $nextWorkingDay = $holidayService->getNextWorkingDay(Carbon::parse($scheduleDate));
                $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextWorkingDay);
            }
        }

        return $scheduledDate;
    }
}
