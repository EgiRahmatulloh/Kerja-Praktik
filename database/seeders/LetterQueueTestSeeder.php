<?php

namespace Database\Seeders;

use App\Models\FilledLetter;
use App\Models\LetterQueue;
use App\Models\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LetterQueueTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan ada jadwal pelayanan aktif
        $serviceSchedule = ServiceSchedule::where('is_active', true)->first();

        if (!$serviceSchedule) {
            // Buat jadwal pelayanan jika belum ada
            $serviceSchedule = ServiceSchedule::create([
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'is_active' => true,
                'processing_time' => 10 // 10 menit per antrian
            ]);
        }

        // Ambil beberapa surat yang sudah disetujui
        $approvedLetters = FilledLetter::where('status', 'approved')
            ->take(5)
            ->get();

        if ($approvedLetters->isEmpty()) {
            // Jika tidak ada surat yang disetujui, ubah beberapa surat menjadi disetujui
            $pendingLetters = FilledLetter::where('status', 'pending')
                ->take(5)
                ->get();

            foreach ($pendingLetters as $letter) {
                $letter->update(['status' => 'approved']);
            }

            $approvedLetters = $pendingLetters;
        }

        // Hapus antrian yang mungkin sudah ada untuk surat-surat ini
        foreach ($approvedLetters as $letter) {
            LetterQueue::where('filled_letter_id', $letter->id)->delete();
        }

        // Buat antrian dengan jadwal yang sudah terlewat
        $yesterday = Carbon::yesterday();
        $twoDaysAgo = Carbon::now()->subDays(2);
        $threeDaysAgo = Carbon::now()->subDays(3);

        // Buat antrian dengan jadwal yang sudah terlewat
        // Hanya gunakan 2 surat pertama untuk membuat antrian duplikat
        $lettersForDuplicates = $approvedLetters->take(2);

        foreach ($lettersForDuplicates as $index => $letter) {
            // Buat jadwal yang berbeda-beda untuk setiap surat
            $date = null;

            switch ($index) {
                case 0:
                    $date = $yesterday->copy()->setTime(9, 0, 0);
                    break;
                case 1:
                    $date = $yesterday->copy()->setTime(10, 0, 0);
                    break;
                default:
                    $date = $yesterday->copy()->setTime(9, 0, 0);
            }

            // Buat antrian pertama
            LetterQueue::create([
                'filled_letter_id' => $letter->id,
                'scheduled_date' => $date,
                'status' => 'waiting',
                'notes' => 'Antrian pengujian #' . ($index + 1)
            ]);

            // Buat antrian duplikat dengan filled_letter_id yang sama
            LetterQueue::create([
                'filled_letter_id' => $letter->id,
                'scheduled_date' => $date->copy()->addHours(1),
                'status' => 'waiting',
                'notes' => 'Antrian duplikat #' . ($index + 1)
            ]);
        }

        $this->command->info('Created ' . count($approvedLetters) . ' test letter queues with expired dates');
    }
}
