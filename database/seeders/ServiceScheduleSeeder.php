<?php

namespace Database\Seeders;

use App\Models\ServiceSchedule;
use Illuminate\Database\Seeder;

class ServiceScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus semua jadwal yang ada
        ServiceSchedule::truncate();

        // Jadwal Pelayanan Pagi: 08:00 - 12:00
        ServiceSchedule::create([
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
            'processing_time' => 10, // 10 menit per antrian
            'is_paused' => false,
            'pause_message' => null,
            'pause_end_time' => null
        ]);

        // Jadwal Pelayanan Siang: 13:00 - 16:00
        ServiceSchedule::create([
            'start_time' => '13:00:00',
            'end_time' => '16:00:00',
            'is_active' => true, // Tidak aktif secara default, hanya satu yang aktif
            'processing_time' => 10, // 10 menit per antrian
            'is_paused' => false,
            'pause_message' => null,
            'pause_end_time' => null
        ]);
    }
}