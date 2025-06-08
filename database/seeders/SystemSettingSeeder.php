<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
use App\Models\FilledLetter;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cari nomor surat tertinggi yang sudah ada
        $lastLetter = FilledLetter::orderBy('no_surat', 'desc')->first();
        $startNumber = $lastLetter ? intval($lastLetter->no_surat) : 0;
        
        // Set nomor surat global
        SystemSetting::set(
            'global_letter_number', 
            $startNumber, 
            'Nomor surat global untuk semua jenis surat'
        );
    }
}
