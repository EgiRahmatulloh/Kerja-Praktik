<?php

namespace Database\Seeders;

use App\Models\FilledLetter;
use App\Models\LetterQueue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestStatusChange extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil surat yang sudah disetujui dan memiliki antrian
        $approvedLetter = FilledLetter::where('status', 'approved')
            ->whereHas('queue')
            ->first();

        if (!$approvedLetter) {
            $this->command->error('Tidak ada surat yang disetujui dengan antrian untuk diuji!');
            return;
        }

        // Tampilkan informasi surat sebelum perubahan
        $this->command->info('Surat ID: ' . $approvedLetter->id);
        $this->command->info('Status sebelum: ' . $approvedLetter->status);

        // Tampilkan informasi antrian sebelum perubahan
        $queue = $approvedLetter->queue;
        if ($queue) {
            $this->command->info('Antrian ID: ' . $queue->id);
            $this->command->info('Jadwal antrian: ' . $queue->scheduled_date);
        } else {
            $this->command->info('Tidak ada antrian untuk surat ini.');
        }

        // Ubah status surat menjadi pending
        $this->command->info('\nMengubah status surat menjadi pending...');
        $approvedLetter->status = 'pending';
        $approvedLetter->save();

        // Refresh model untuk mendapatkan data terbaru
        $approvedLetter->refresh();

        // Tampilkan informasi surat setelah perubahan
        $this->command->info('Status setelah: ' . $approvedLetter->status);

        // Periksa apakah antrian sudah dihapus
        $queueAfter = LetterQueue::where('filled_letter_id', $approvedLetter->id)->first();
        if ($queueAfter) {
            $this->command->error('Antrian masih ada! ID: ' . $queueAfter->id);
        } else {
            $this->command->info('Antrian berhasil dihapus!');
        }
    }
}
