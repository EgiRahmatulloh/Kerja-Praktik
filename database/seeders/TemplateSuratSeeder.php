<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateSuratSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \App\Models\TemplateSurat::create([
      'nama_template' => 'Surat Keterangan Domisili',
      'konten_template' => '<center>
      <table width="450">
        <tr>
          <td><img src="{{ asset(\'dashmin/img/logo_cetak.svg\') }}" width="110" height="110" /></td>
          <td style="font-family: \'Times New Roman\', Times, serif; font-size: 13px">
            <center>
              <font size="5"><b>PEMERINTAH KOTA LHOKSEUMAWE</b> </font><br />
              <font size="4"><b>KECAMATAN MUARA DUA</b></font
              ><br />
              <font size="5"><b>GAMPONG PAYA PUNTEUET</b></font
              ><br />
              <font size="3"><i>JLN. Tgk, WAHAB DAHLAWI KM.1</i></font
              ><br />
            </center>
          </td>
        </tr>
        <tr>
          <td colspan="2"><hr style="border: 1px solid" /></td>
        </tr>
      </table>
      <br />
      <table width="450">
        <tr>
          <td style="font-family: \'Times New Roman\', Times, serif; font-size: 18px; text-align: center; font-weight: bold" class="text">
            <u>SURAT KETERANGAN DOMISILI</u>
          </td>
        </tr>
        <tr>
          <td style="text-align: center">Nomor : {{ $data->kodeSurat }}/{{ $data->noSurat }}/{{ $data->tahun }}</td>
        </tr>
      </table>
      <br /><br /><br />
      <table width="450">
        <tr>
          <td>Keuchik Gampong Paya Punteuet Kecamatan Muara Dua Pemerintah Kota Lhokseumawe, dengan ini menerangkan bahwa :</td>
        </tr>
      </table>
      <br /><br />
      <table width="450">
        <tr>
          <td width="120">Nama</td>
          <td width="10">:</td>
          <td width="335">{{ $data->nama }}</td>
        </tr>
        <tr>
          <td width="120">NIK</td>
          <td width="10">:</td>
          <td width="335">{{ $data->nik }}</td>
        </tr>
        <tr>
          <td width="120">Tempat, Tanggal Lahir</td>
          <td width="10">:</td>
          <td width="335">{{ $data->tempatTglLahir }}</td>
        </tr>
        <tr>
          <td width="120">Pekerjaan</td>
          <td width="10">:</td>
          <td width="335">{{ $data->pekerjaan }}</td>
        </tr>
        <tr>
          <td width="120">Alamat</td>
          <td width="10">:</td>
          <td width="335">{{ $data->alamat }}</td>
        </tr>
      </table>
      <br /><br />
      <table width="450">
        <tr>
          <td >
            {!! $data->keterangan !!}
          </td>
        </tr>
        <br /><br />
      </table>
      <br /><br />
      <table width="450">
        <tr>
          <td >Demikian surat keterangan ini kami perbuat untuk dapat dipergunakan seperlunya.</td>
        </tr>
      </table>
      <br /><br /><br /><br>
      <table width="450">
        <tr>
          <td width="300"></td>
          <td style="text-align: left">Paya Punteuet, {{ date(\'d M Y\', strtotime($data->tglSurat)); }}</td>
        </tr>
        <tr>
          <td width="300"></td>
          <td style="text-align: left">An. {{ $data->ttd }}</td>
        </tr>
      </table>
      <br /><br />
      <table width="450" style="margin-bottom: 100px">
        <tr>
          <td width="300"></td>
          <td style="text-align: left">{{ $data->namaTtd }}</td>
        </tr>
      </table>
    </center>'
    ]);

    \App\Models\TemplateSurat::create([
      'nama_template' => 'Surat Keterangan Usaha',
      'konten_template' => '<center>
      <table width="450">
        <tr>
          <td><img src="{{ asset(\'dashmin/img/logo_cetak.svg\') }}" width="110" height="110" /></td>
          <td style="font-family: \'Times New Roman\', Times, serif; font-size: 13px">
            <center>
              <font size="5"><b>PEMERINTAH KOTA LHOKSEUMAWE</b> </font><br />
              <font size="4"><b>KECAMATAN MUARA DUA</b></font
              ><br />
              <font size="5"><b>GAMPONG PAYA PUNTEUET</b></font
              ><br />
              <font size="3"><i>JLN. Tgk, WAHAB DAHLAWI KM.1</i></font
              ><br />
            </center>
          </td>
          
        </tr>
        <tr>
          <td colspan="2"><hr style="border: 1px solid" /></td>
        </tr>
      </table>
      <br />
      <table width="450">
        <tr>
          <td style="font-family: \'Times New Roman\', Times, serif; font-size: 18px; text-align: center; font-weight: bold" class="text">
            <u>SURAT KETERANGAN USAHA</u>
          </td>
        </tr>
        <tr>
          <td style="text-align: center">Nomor : {{ $data->kodeSurat }}/{{ $data->noSurat }}/{{ $data->tahun }}</td>
        </tr>
      </table>
      <br /><br /><br />
      <table width="450">
        <tr>
          <td>Keuchik Gampong Paya Punteuet Kecamatan Muara Dua Pemerintah Kota Lhokseumawe, dengan ini menerangkan bahwa :</td>
        </tr>
      </table>
      <br /><br />
      <table width="450">
        <tr>
          <td width="120">Nama</td>
          <td width="10">:</td>
          <td width="335">{{ $data->nama }}</td>
        </tr>
        <tr>
          <td width="120">NIK</td>
          <td width="10">:</td>
          <td width="335">{{ $data->nik }}</td>
        </tr>
        <tr>
          <td width="120">Tempat, Tanggal Lahir</td>
          <td width="10">:</td>
          <td width="335">{{ $data->tempatTglLahir }}</td>
        </tr>
        <tr>
          <td width="120">Pekerjaan</td>
          <td width="10">:</td>
          <td width="335">{{ $data->pekerjaan }}</td>
        </tr>
        <tr>
          <td width="120">Alamat</td>
          <td width="10">:</td>
          <td width="335">{{ $data->alamat }}</td>
        </tr>
      </table>
      <br /><br />
      <table width="450">
        <tr>
          <td>
            {!! $data->keterangan !!}
          </td>
        </tr>
        <br /><br />
      </table>
      <br /><br />
      <table width="450">
        <tr>
          <td>Demikian surat keterangan ini kami perbuat untuk dapat dipergunakan seperlunya.</td>
        </tr>
      </table>
      <br /><br /><br />
      <table width="450">
        <tr>
          <td width="300"></td>
          <td style="text-align: left">Paya Punteuet, {{ date(\'d M Y\', strtotime($data->tglSurat)); }}</td>
        </tr>
        <tr>
          <td width="300"></td>
          <td style="text-align: left">An. {{ $data->ttd }}</td>
        </tr>
      </table>
      <br /><br />
      <table width="450" style="margin-bottom: 100px">
        <tr>
            <td width="300"></td>
            <td style="text-align: left">{{ $data->namaTtd }}</td>
        </tr>
      </table>
    </center>'
    ]);

    // Tambahkan template surat pengantar KP
    \App\Models\TemplateSurat::create([
      'nama_template' => 'Surat Pengantar KP',
      'html_content' => '<center>
      <table width="450">
        <tr>
          <td><img src="{{ asset(\'dashmin/img/logo_cetak.svg\') }}" width="110" height="110" /></td>
          <td style="font-family: \'Times New Roman\', Times, serif; font-size: 13px">
            <center>
              <font size="5"><b>PEMERINTAH KOTA LHOKSEUMAWE</b> </font><br />
              <font size="4"><b>KECAMATAN MUARA DUA</b></font
              ><br />
              <font size="5"><b>GAMPONG PAYA PUNTEUET</b></font
              ><br />
              <font size="3"><i>JLN. Tgk, WAHAB DAHLAWI KM.1</i></font
              ><br />
            </center>
          </td>
        </tr>
        <tr>
          <td colspan="2"><hr style="border: 1px solid" /></td>
        </tr>
      </table>
      <br />
      <table width="450">
        <tr>
          <td style="font-family: \'Times New Roman\', Times, serif; font-size: 18px; text-align: center; font-weight: bold" class="text">
            <u>SURAT PENGANTAR KERJA PRAKTEK</u>
          </td>
        </tr>
        <tr>
          <td style="text-align: center">Nomor : {{ $data->nomor }}/{{ $data->kodeSurat }}/{{ $data->noSurat }}/{{ $data->tahun }}</td>
        </tr>
      </table>
      <br />
      <table width="450" style="text-align: justify">
        <tr>
          <td>Yang bertanda tangan di bawah ini:</td>
        </tr>
      </table>
      <br />
      <table width="450">
        <tr>
          <td width="150">Nama</td>
          <td width="10">:</td>
          <td>{{ $data->namaTtd }}</td>
        </tr>
        <tr>
          <td>Jabatan</td>
          <td>:</td>
          <td>{{ $data->ttd }}</td>
        </tr>
      </table>
      <br />
      <table width="450" style="text-align: justify">
        <tr>
          <td>Dengan ini menerangkan bahwa:</td>
        </tr>
      </table>
      <br />
      <table width="450">
        <tr>
          <td width="150">Nama</td>
          <td width="10">:</td>
          <td>{{ $data->nama }}</td>
        </tr>
        <tr>
          <td>NIM</td>
          <td>:</td>
          <td>{{ $data->nim }}</td>
        </tr>
        <tr>
          <td>Judul KP</td>
          <td>:</td>
          <td>{{ $data->judul }}</td>
        </tr>
      </table>
      <br />
      <table width="450" style="text-align: justify">
        <tr>
          <td>Adalah benar mahasiswa yang akan melaksanakan Kerja Praktek di {{ $data->tujuan }} dengan alamat {{ $data->alamatInstitusi }}.</td>
        </tr>
        <tr>
          <td>Demikian surat pengantar ini kami berikan untuk dapat dipergunakan sebagaimana mestinya.</td>
        </tr>
      </table>
      <br /><br /><br />
      <table width="450">
        <tr>
          <td width="300"></td>
          <td style="text-align: left">Paya Punteuet, {{ date(\'d M Y\', strtotime($data->tglSurat)); }}</td>
        </tr>
        <tr>
          <td width="300"></td>
          <td style="text-align: left">{{ $data->ttd }}</td>
        </tr>
      </table>
      <br /><br />
      <table width="450" style="margin-bottom: 100px">
        <tr>
            <td width="300"></td>
            <td style="text-align: left">{{ $data->namaTtd }}</td>
        </tr>
      </table>
    </center>',
      'aktif' => true
    ]);
  }
}
