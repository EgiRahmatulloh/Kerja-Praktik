<?php

namespace Database\Seeders;

use App\Models\DataItem;
use App\Models\LetterType;
use App\Models\TemplateSurat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterTypeSeeder extends Seeder
{
    public function run()
    {
        // Mendapatkan template surat
        $templateDomisili = TemplateSurat::where('nama_template', 'Surat Keterangan Domisili')->first();
        $templateUsaha = TemplateSurat::where('nama_template', 'Surat Keterangan Usaha')->first();
        
        // Membuat jenis surat domisili
        $suratDomisili = LetterType::create([
            'nama_jenis' => 'Surat Keterangan Domisili',
            'template_surat_id' => $templateDomisili->id
        ]);
        
        // Membuat jenis surat usaha
        $suratUsaha = LetterType::create([
            'nama_jenis' => 'Surat Keterangan Usaha',
            'template_surat_id' => $templateUsaha->id
        ]);
        
        // Data items untuk surat domisili
        $dataDomisili = [
            'nama', 'nik', 'tempatTglLahir', 'pekerjaan', 'alamat', 'keterangan',
            'tglSurat', 'ttd', 'namaTtd'
        ];
        
        // Data items untuk surat usaha
        $dataUsaha = [
            'nama', 'nik', 'tempatTglLahir', 'pekerjaan', 'alamat', 'keterangan',
            'namaUsaha', 'jenisUsaha', 'alamatUsaha',
            'tglSurat', 'ttd', 'namaTtd'
        ];
        
        // Menghubungkan data items dengan surat domisili
        foreach ($dataDomisili as $key) {
            $dataItem = DataItem::where('key', $key)->first();
            if ($dataItem) {
                $suratDomisili->dataItems()->attach($dataItem->id);
            }
        }
        
        // Menghubungkan data items dengan surat usaha
        foreach ($dataUsaha as $key) {
            $dataItem = DataItem::where('key', $key)->first();
            if ($dataItem) {
                $suratUsaha->dataItems()->attach($dataItem->id);
            }
        }
        
        // Tambahkan template surat pengantar KP (Anda perlu membuat template ini di TemplateSuratSeeder.php)
        $templatePengantarKP = TemplateSurat::where('nama_template', 'Surat Pengantar KP')->first();
        
        // Jika template belum ada, gunakan template default sementara
        if (!$templatePengantarKP) {
            $templatePengantarKP = TemplateSurat::first();
        }
        
        // Membuat jenis surat pengantar KP
        $suratPengantarKP = LetterType::create([
            'nama_jenis' => 'Surat Pengantar KP',
            'template_surat_id' => $templatePengantarKP->id
        ]);
        
        // Data items untuk surat pengantar KP
        $dataPengantarKP = [
            'nama', 'nim', 'nomor', 'lampiran', 'perihal', 'tujuan', 'alamatInstitusi',
            'judul', 'nidn', 'tglSurat', 'ttd', 'namaTtd'
        ];
        
        // Menghubungkan data items dengan surat pengantar KP
        foreach ($dataPengantarKP as $key) {
            $dataItem = DataItem::where('key', $key)->first();
            if ($dataItem) {
                $suratPengantarKP->dataItems()->attach($dataItem->id);
            }
        }
    }
}
