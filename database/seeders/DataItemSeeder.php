<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\DataItem::truncate(); // Clear existing data

        // Data items untuk semua jenis surat
        \App\Models\DataItem::create([
            'key' => 'nama',
            'label' => 'Nama Lengkap',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'nim',
            'label' => 'NIM Mahasiswa',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'judulKP',
            'label' => 'Judul Kerja Praktik',
            'tipe_input' => 'textarea'
        ]);
    }
}
