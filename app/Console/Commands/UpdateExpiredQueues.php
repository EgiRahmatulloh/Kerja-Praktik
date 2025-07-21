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

        $now = Carbon::now();
        $expiredQueues = LetterQueue::where('status', 'waiting')
            ->where('scheduled_date', '<', $now)
            ->orderBy('id', 'asc')
            ->get();

        if ($expiredQueues->isEmpty()) {
            $this->info('No expired queues found.');
            return 0;
        }

        // Kelompokkan antrian berdasarkan jadwal layanan
        $groupedQueues = $expiredQueues->groupBy('service_schedule_id');

        foreach ($groupedQueues as $serviceScheduleId => $queues) {
            $serviceSchedule = ServiceSchedule::find($serviceScheduleId);
            if (!$serviceSchedule || !$serviceSchedule->is_active) {
                $this->warn("Service schedule #{$serviceScheduleId} is not active or not found, skipping.");
                continue;
            }

            $this->info("Processing schedule for user: {$serviceSchedule->user->name}");
            $this->processExpiredQueuesForSchedule($serviceSchedule, $queues);
        }

        $this->info('All expired queues have been updated successfully.');
        return 0;
    }

    private function processExpiredQueuesForSchedule($serviceSchedule, $expiredQueues)
    {
        $uniqueQueues = $expiredQueues->unique('filled_letter_id')->values();

        if ($uniqueQueues->isEmpty()) {
            $this->info('No unique expired queues to process for this schedule.');
            return;
        }

        $this->info('Found ' . $uniqueQueues->count() . ' unique expired queues for this schedule.');

        $holidayService = new HolidayService();
        $processingTime = $serviceSchedule->processing_time;

        // Find the last scheduled time for this service to determine where to start adding new queues.
        $lastSchedule = LetterQueue::where('service_schedule_id', $serviceSchedule->id)
                                ->orderBy('scheduled_date', 'desc')
                                ->first();

        // Start scheduling after the last queue, or from now, whichever is later.
        $nextAvailableTime = Carbon::now();
        if ($lastSchedule) {
            $endOfLastQueue = Carbon::parse($lastSchedule->scheduled_date)->addMinutes($processingTime);
            if ($endOfLastQueue->isAfter($nextAvailableTime)) {
                $nextAvailableTime = $endOfLastQueue;
            }
        }

        foreach ($uniqueQueues as $queue) {
            // Ensure the $nextAvailableTime is a valid, bookable slot.
            while (true) {
                $day = $nextAvailableTime->copy()->startOfDay();

                // 1. Check for holidays
                if ($holidayService->isHoliday($day)) {
                    // If it's a holiday, advance to the start of the next day.
                    $nextAvailableTime = $day->addDay()->setTimeFromTimeString($serviceSchedule->start_time);
                    continue; // Re-run checks for the new day.
                }

                $startTime = $day->copy()->setTimeFromTimeString($serviceSchedule->start_time);
                $endTime = $day->copy()->setTimeFromTimeString($serviceSchedule->end_time);

                // 2. If current time is before service hours, move to start time.
                if ($nextAvailableTime->isBefore($startTime)) {
                    $nextAvailableTime = $startTime;
                }

                // 3. If current time is after service hours, advance to the next day.
                if ($nextAvailableTime->isAfter($endTime)) {
                    $nextAvailableTime = $day->addDay()->setTimeFromTimeString($serviceSchedule->start_time);
                    continue; // Re-run checks for the new day.
                }

                // If all checks pass, this is a valid slot.
                break;
            }

            // Update the queue with the valid time slot.
            $queue->update(['scheduled_date' => $nextAvailableTime]);
            $this->info("Updated queue #{$queue->id} to {$nextAvailableTime->toDateTimeString()}");

            // Increment the time for the next queue.
            $nextAvailableTime->addMinutes($processingTime);
        }

        $this->info('All expired queues for this schedule have been updated successfully.');
    }
}
