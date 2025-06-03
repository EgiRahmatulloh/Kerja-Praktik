<?php

namespace App\Console\Commands;

use App\Models\LetterQueue;
use Illuminate\Console\Command;

class ShowLetterQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queues:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all letter queues';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queues = LetterQueue::where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->get();

        if ($queues->isEmpty()) {
            $this->info('No waiting queues found.');
            return Command::SUCCESS;
        }

        $headers = ['ID', 'Filled Letter ID', 'Scheduled Date', 'Status', 'Notes'];
        $rows = [];

        foreach ($queues as $queue) {
            $rows[] = [
                $queue->id,
                $queue->filled_letter_id,
                $queue->scheduled_date->format('Y-m-d H:i:s'),
                $queue->status,
                $queue->notes ?? '-'
            ];
        }

        $this->table($headers, $rows);
        return Command::SUCCESS;
    }
}
