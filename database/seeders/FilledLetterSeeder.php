<?php

namespace Database\Seeders;

use App\Models\FilledLetter;
use App\Models\LetterType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilledLetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mendapatkan data user dan jenis surat
        $userAdmin = User::where('role', 'admin')->first();
        $userBiasa = User::where('role', 'user')->first();
        $suratDomisili = LetterType::where('nama_jenis', 'Surat Keterangan Domisili')->first();
        $suratUsaha = LetterType::where('nama_jenis', 'Surat Keterangan Usaha')->first();
        
        // Contoh data untuk surat domisili
        $dataDomisili1 = [
            'nama' => 'Budi Santoso',
            'nik' => '1111222233334444',
            'tempatTglLahir' => 'Jakarta, 15 Agustus 1990',
            'pekerjaan' => 'Wiraswasta',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta Selatan',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2015 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili2 = [
            'nama' => 'Siti Rahayu',
            'nik' => '2222333344445555',
            'tempatTglLahir' => 'Bandung, 22 Mei 1985',
            'pekerjaan' => 'Guru',
            'alamat' => 'Jl. Pendidikan No. 45, Jakarta Timur',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2018 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili3 = [
            'nama' => 'Agus Setiawan',
            'nik' => '5555666677778888',
            'tempatTglLahir' => 'Yogyakarta, 10 April 1995',
            'pekerjaan' => 'Mahasiswa',
            'alamat' => 'Jl. Pelajar No. 30, Jakarta Selatan',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2020 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili4 = [
            'nama' => 'Linda Wijaya',
            'nik' => '6666777788889999',
            'tempatTglLahir' => 'Semarang, 25 September 1988',
            'pekerjaan' => 'Pegawai Swasta',
            'alamat' => 'Jl. Kembang No. 50, Jakarta Timur',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2017 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili5 = [
            'nama' => 'Rudi Hartono',
            'nik' => '7777888899990000',
            'tempatTglLahir' => 'Malang, 01 Maret 1993',
            'pekerjaan' => 'Freelancer',
            'alamat' => 'Jl. Damai No. 70, Jakarta Utara',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2021 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili6 = [
            'nama' => 'Maya Sari',
            'nik' => '8888999900001111',
            'tempatTglLahir' => 'Surakarta, 18 Juli 1987',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'alamat' => 'Jl. Bahagia No. 90, Jakarta Pusat',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2016 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili7 = [
            'nama' => 'Faisal Rahman',
            'nik' => '9999000011112222',
            'tempatTglLahir' => 'Bogor, 07 November 1998',
            'pekerjaan' => 'Pelajar',
            'alamat' => 'Jl. Ceria No. 110, Jakarta Selatan',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2022 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili8 = [
            'nama' => 'Dewi Lestari',
            'nik' => '0000111122223333',
            'tempatTglLahir' => 'Depok, 14 Februari 1983',
            'pekerjaan' => 'Dokter',
            'alamat' => 'Jl. Sehat No. 130, Jakarta Timur',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2014 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili9 = [
            'nama' => 'Eko Prasetyo',
            'nik' => '1212343456567878',
            'tempatTglLahir' => 'Bekasi, 29 Juni 1991',
            'pekerjaan' => 'Insinyur',
            'alamat' => 'Jl. Teknologi No. 150, Jakarta Utara',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2019 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataDomisili10 = [
            'nama' => 'Fitriani',
            'nik' => '3434565678789090',
            'tempatTglLahir' => 'Tangerang, 03 Desember 1989',
            'pekerjaan' => 'Akuntan',
            'alamat' => 'Jl. Keuangan No. 170, Jakarta Pusat',
            'keterangan' => 'Yang bersangkutan benar merupakan penduduk yang berdomisili di alamat tersebut sejak tahun 2013 sampai sekarang.',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        // Contoh data untuk surat usaha
        $dataUsaha1 = [
            'nama' => 'Joko Widodo',
            'nik' => '3333444455556666',
            'tempatTglLahir' => 'Solo, 10 Juni 1980',
            'pekerjaan' => 'Pengusaha',
            'alamat' => 'Jl. Industri No. 78, Jakarta Utara',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Mebel Jaya Abadi',
            'jenisUsaha' => 'Perdagangan Mebel',
            'alamatUsaha' => 'Jl. Industri No. 78, Jakarta Utara',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha2 = [
            'nama' => 'Dewi Kartika',
            'nik' => '4444555566667777',
            'tempatTglLahir' => 'Surabaya, 5 Januari 1992',
            'pekerjaan' => 'Pengusaha',
            'alamat' => 'Jl. Pasar Baru No. 15, Jakarta Pusat',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Butik Kartika',
            'jenisUsaha' => 'Fashion dan Pakaian',
            'alamatUsaha' => 'Jl. Pasar Baru No. 15, Jakarta Pusat',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha3 = [
            'nama' => 'Bambang Susilo',
            'nik' => '5656787890901212',
            'tempatTglLahir' => 'Purwokerto, 12 Januari 1986',
            'pekerjaan' => 'Pedagang',
            'alamat' => 'Jl. Niaga No. 20, Jakarta Selatan',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Toko Kelontong Maju',
            'jenisUsaha' => 'Perdagangan Umum',
            'alamatUsaha' => 'Jl. Niaga No. 20, Jakarta Selatan',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha4 = [
            'nama' => 'Citra Kirana',
            'nik' => '7878909012123434',
            'tempatTglLahir' => 'Cirebon, 28 Mei 1994',
            'pekerjaan' => 'Pengusaha Kuliner',
            'alamat' => 'Jl. Rasa No. 40, Jakarta Timur',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Warung Makan Enak',
            'jenisUsaha' => 'Kuliner',
            'alamatUsaha' => 'Jl. Rasa No. 40, Jakarta Timur',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha5 = [
            'nama' => 'Dedi Iskandar',
            'nik' => '9090121234345656',
            'tempatTglLahir' => 'Sukabumi, 09 September 1981',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Tani No. 60, Jakarta Utara',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Kebun Subur',
            'jenisUsaha' => 'Pertanian',
            'alamatUsaha' => 'Jl. Tani No. 60, Jakarta Utara',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha6 = [
            'nama' => 'Eka Putri',
            'nik' => '1212343456567878',
            'tempatTglLahir' => 'Tasikmalaya, 04 April 1996',
            'pekerjaan' => 'Pengrajin',
            'alamat' => 'Jl. Karya No. 80, Jakarta Pusat',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Kerajinan Tangan Unik',
            'jenisUsaha' => 'Kerajinan',
            'alamatUsaha' => 'Jl. Karya No. 80, Jakarta Pusat',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha7 = [
            'nama' => 'Gatot Subroto',
            'nik' => '3434565678789090',
            'tempatTglLahir' => 'Garut, 21 Oktober 1984',
            'pekerjaan' => 'Peternak',
            'alamat' => 'Jl. Ternak No. 100, Jakarta Selatan',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Peternakan Sejahtera',
            'jenisUsaha' => 'Peternakan',
            'alamatUsaha' => 'Jl. Ternak No. 100, Jakarta Selatan',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha8 = [
            'nama' => 'Haniifah',
            'nik' => '5656787890901212',
            'tempatTglLahir' => 'Cianjur, 17 Maret 1997',
            'pekerjaan' => 'Pengusaha Digital',
            'alamat' => 'Jl. Digital No. 120, Jakarta Timur',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Solusi Digital Kreatif',
            'jenisUsaha' => 'Teknologi Informasi',
            'alamatUsaha' => 'Jl. Digital No. 120, Jakarta Timur',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha9 = [
            'nama' => 'Indra Permana',
            'nik' => '7878909012123434',
            'tempatTglLahir' => 'Subang, 06 Agustus 1982',
            'pekerjaan' => 'Nelayan',
            'alamat' => 'Jl. Laut No. 140, Jakarta Utara',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Hasil Laut Segar',
            'jenisUsaha' => 'Perikanan',
            'alamatUsaha' => 'Jl. Laut No. 140, Jakarta Utara',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        $dataUsaha10 = [
            'nama' => 'Juwita',
            'nik' => '9090121234345656',
            'tempatTglLahir' => 'Karawang, 23 November 1990',
            'pekerjaan' => 'Pengusaha Fashion',
            'alamat' => 'Jl. Gaya No. 160, Jakarta Pusat',
            'keterangan' => 'Yang bersangkutan benar memiliki usaha sebagaimana tersebut di atas yang berlokasi di wilayah kami.',
            'namaUsaha' => 'Butik Anggun',
            'jenisUsaha' => 'Fashion dan Pakaian',
            'alamatUsaha' => 'Jl. Gaya No. 160, Jakarta Pusat',
            'tglSurat' => Carbon::now()->format('Y-m-d'),
            'tahun' => Carbon::now()->format('Y'),
            'ttd' => 'Kepala Desa',
            'namaTtd' => 'H. Ahmad Suparjo'
        ];
        
        // Membuat surat domisili dengan status berbeda
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili1,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili2,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili3,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili4,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili5,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili6,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili7,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili8,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili9,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratDomisili->id,
            'filled_data' => $dataDomisili10,
            'status' => 'pending'
        ]);
        
        // Membuat surat usaha dengan status berbeda
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha1,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha2,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha3,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha4,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha5,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha6,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha7,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha8,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha9,
            'status' => 'pending'
        ]);
        
        FilledLetter::create([
            'user_id' => $userBiasa->id,
            'letter_type_id' => $suratUsaha->id,
            'filled_data' => $dataUsaha10,
            'status' => 'pending'
        ]);
    }
}
