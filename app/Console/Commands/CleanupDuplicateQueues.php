<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LetterQueue;

class CleanupDuplicateQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queues:cleanup-duplicates {--status=waiting : Status antrian yang akan dibersihkan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan antrian duplikat berdasarkan filled_letter_id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = $this->option('status');
        $this->info("Membersihkan antrian duplikat dengan status '{$status}'...");

        // Ambil semua antrian dengan status yang ditentukan
        $queues = LetterQueue::where('status', $status)->get();

        if ($queues->isEmpty()) {
            $this->info("Tidak ada antrian dengan status '{$status}'.");
            return 0;
        }

        $this->info("Ditemukan {$queues->count()} antrian dengan status '{$status}'.");

        // Kelompokkan berdasarkan filled_letter_id
        $groupedQueues = $queues->groupBy('filled_letter_id');

        $deletedCount = 0;
        $duplicateGroups = 0;

        // Untuk setiap kelompok, simpan hanya antrian dengan ID terkecil
        foreach ($groupedQueues as $filledLetterId => $queueGroup) {
            if ($queueGroup->count() > 1) {
                $duplicateGroups++;

                // Urutkan berdasarkan ID
                $sortedQueues = $queueGroup->sortBy('id');

                // Simpan yang pertama (ID terkecil)
                $keepQueue = $sortedQueues->first();
                $this->info("Mempertahankan antrian #{$keepQueue->id} untuk surat #{$filledLetterId}");

                // Hapus sisanya
                foreach ($sortedQueues as $queue) {
                    if ($queue->id != $keepQueue->id) {
                        $this->info("Menghapus antrian duplikat #{$queue->id} untuk surat #{$filledLetterId}");
                        $queue->delete();
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Berhasil menghapus {$deletedCount} antrian duplikat dari {$duplicateGroups} kelompok.");
        } else {
            $this->info("Tidak ditemukan antrian duplikat.");
        }

        return 0;
    }
}
