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
        // Data items untuk semua jenis surat
        \App\Models\DataItem::create([
            'key' => 'nama',
            'label' => 'Nama Lengkap',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'nik',
            'label' => 'NIK',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'tempatTglLahir',
            'label' => 'Tempat, Tanggal Lahir',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'pekerjaan',
            'label' => 'Pekerjaan',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'alamat',
            'label' => 'Alamat',
            'tipe_input' => 'textarea'
        ]);

        \App\Models\DataItem::create([
            'key' => 'keterangan',
            'label' => 'Keterangan',
            'tipe_input' => 'textarea'
        ]);

        // Data items khusus untuk surat usaha
        \App\Models\DataItem::create([
            'key' => 'namaUsaha',
            'label' => 'Nama Usaha',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'jenisUsaha',
            'label' => 'Jenis Usaha',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'alamatUsaha',
            'label' => 'Alamat Usaha',
            'tipe_input' => 'textarea'
        ]);

        // Data items untuk administrasi surat
        \App\Models\DataItem::create([
            'key' => 'tglSurat',
            'label' => 'Tanggal Surat',
            'tipe_input' => 'date'
        ]);

        \App\Models\DataItem::create([
            'key' => 'ttd',
            'label' => 'Tanda Tangan',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'namaTtd',
            'label' => 'Nama Penandatangan',
            'tipe_input' => 'text'
        ]);
        // Tambahkan di DataItemSeeder.php atau buat seeder baru

        // Variabel untuk surat pengantar KP
        \App\Models\DataItem::create([
            'key' => 'nomor',
            'label' => 'Nomor Surat',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'lampiran',
            'label' => 'Lampiran',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'perihal',
            'label' => 'Perihal',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'tujuan',
            'label' => 'Tujuan Surat',
            'tipe_input' => 'text'
        ]);

        // Mengganti fakultas, universitas, dan kota dengan alamat institusi penerima
        \App\Models\DataItem::create([
            'key' => 'alamatInstitusi',
            'label' => 'Alamat Institusi Penerima',
            'tipe_input' => 'textarea'
        ]);

        \App\Models\DataItem::create([
            'key' => 'nim',
            'label' => 'NIM Mahasiswa',
            'tipe_input' => 'text'
        ]);

        \App\Models\DataItem::create([
            'key' => 'judul',
            'label' => 'Judul Kegiatan/Penelitian',
            'tipe_input' => 'textarea'
        ]);

        \App\Models\DataItem::create([
            'key' => 'nidn',
            'label' => 'NIDN Penandatangan',
            'tipe_input' => 'text'
        ]);
    }
}
