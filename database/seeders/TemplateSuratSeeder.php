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
     'nama_template' => 'Surat Pengantar Izin Kerja Praktik (KP)',
     'konten_template' => '<div class="container">
       <table width="450" style="border-collapse: collapse;">
           <tbody>
               <tr>
                   <td style="width: 110px; text-align: left;">
                       <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/refs/heads/main/logo_unper.png" alt="Logo Universitas" style="width: 110px; height: 110px; display: block; margin: 0 auto;" onerror="this.onerror=null;this.src=\'https://placehold.co/110x110/cccccc/333333?text=Logo\';">
                   </td>
                   <td style="font-family: \'Times New Roman\', Times, serif; font-size: 13px; text-align: center;">
                       <center>
                           <font size="5"><b><span style="font-size: 14px;">YAYASAN UNIVERSITAS SILIWANGI</span></b></font><br>
                           <font size="4"><b><span style="font-size: 14px;">UNIVERSITAS PERJUANGAN TASIKMALAYA</span></b></font><br>
                           <font size="5"><b><span style="font-size: 14px;">FAKULTAS TEKNIK</span></b></font><br>
                           <font size="3"><i><span style="font-size: 11px;">Jalan Pembela Tanah Air (PETA) No. 177 Kota Tasikmalaya, Kode Pos 46115</span></i></font><br>
                           <font size="3"><i><span style="font-size: 11px;">Telepon (0265) 326058, laman : http://www.unper.ac.id</span></i></font>
                       </center>
                   </td>
                   <td style="width: 110px; text-align: right;">
                       <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/515b4b45417676e20e79f06fad8437d91c97c2cb/image.png" alt="Logo Yayasan" style="width: 110px; height: 110px; display: block; margin: 0 auto;" onerror="this.onerror=null;this.src=\'https://placehold.co/110x110/cccccc/333333?text=Logo\';">
                   </td>
               </tr>
               <tr>
                   <td colspan="3"><hr style="border: 1px solid black;"></td>
               </tr>
           </tbody>
       </table>
       <br>

       <table width="450" style="border-collapse: collapse; margin-top: 10px;">
           <tbody>
               <tr>
                   <td colspan="3" style="text-align: right;"><font size="2">Tasikmalaya, {{ \$tanggal }}</font></td>
               </tr>
           </tbody>
       </table>

       <br>

       <table width="450" style="border-collapse: collapse;">
           <tbody>
               <tr>
                   <td width="70"><font size="2">Nomor</font></td>
                   <td width="10"><font size="2">:</font></td>
                   <td>{{ $data->noSurat }}<font size="2">/KP/FT-UP/</font>{{ $data->bulan }}<font size="2">/</font><span style="font-size: 1rem;">{{ $data->tahun }}</span><br></td>
               </tr>
               <tr>
                   <td><font size="2">Lampiran</font></td>
                   <td><font size="2">:</font></td>
                   <td>-</td>
               </tr>
               <tr>
                   <td><font size="2">Perihal</font></td>
                   <td><font size="2">:</font></td>
                   <td><font size="2">Pengantar Izin Kerja Praktik (KP)</font></td>
               </tr>
           </tbody>
       </table>

       <br>

       <table width="450" style="border-collapse: collapse;">
           <tbody>
               <tr>
                   <td width="70"><font size="2">Kepada Yth</font></td>
                   <td width="10"><font size="2">:</font></td><td>Dekan</td></tr></tbody></table></div><div class="container"><table width="450" style="border-collapse: collapse;"><tbody><tr><td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Fakultas Teknik Universitas Perjuangan</td></tr></tbody></table></div><div class="container"><table width="450" style="border-collapse: collapse;"><tbody><tr><td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Jl. Peta No. 177, Kahuripan, kec. Tawang, Kota</td></tr></tbody></table></div><div class="container"><table width="450" style="border-collapse: collapse;"><tbody><tr><td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Tasikmalaya</td>
               </tr>
           </tbody>
       </table>

       <br>

       <table width="450" style="border-collapse: collapse;">
           <tbody>
               <tr>
                   <td width="80"></td>
                   <td colspan="2">
                       <font size="2">Disampaikan dengan hormat,</font>
                   </td>
               </tr>
               <tr>
                   <td width="80"></td>
                   <td colspan="2">
                       <font size="2">
                       <br>
                       Sehubungan surat ini ....................................................................................................................................................................................................
                       <br><br>
                       Maka kami bermaksud untuk menyampaikan surat ini sebagai pengantar:
                       <br><br>
                       <table width="350" style="border-collapse: collapse;">
                           <tbody><tr>
                               <td width="50">Nama</td>
                               <td width="10">:</td>
                               <td>{{ $nama }}</td></tr><tr><td>NIM</td>
                               <td>:</td>
                               <td>{{ $nim }}</td></tr></tbody></table></font></td></tr></tbody></table></div><div class="container"><table width="450" style="border-collapse: collapse;"><tbody><tr><td colspan="2"><font size="2"><table width="350" style="border-collapse: collapse;"><tbody><tr><td>Judul&nbsp; &nbsp; &nbsp; :&nbsp; {{ $judul }}</td></tr></tbody></table></font></td></tr></tbody></table></div><div class="container"><table width="450" style="border-collapse: collapse;"><tbody><tr><td colspan="2"><font size="2"><table width="350" style="border-collapse: collapse;"><tbody><tr><td>Tempat&nbsp; &nbsp;: {{ $alamatInstitusi }}</td>
                           </tr>
                       </tbody></table>
                       <br>
                       Demikianlah surat pengantar<br>
                       Atas perhatiannya kami ucapkan terimakasih.
                       </font>
                   </td>
               </tr>
           </tbody>
       </table>

       <br><br>

       <table width="450" style="border-collapse: collapse;">
           <tbody>
               <tr>
                   <td width="300"></td>
                   <td style="text-align: left;">
                       <font size="2">
                       Dekan Fakultas Teknik
                       <br><br><br><br>
                       Tanda tangan dan cap jabatan
                       <br><br>
                       NAMA PEJABAT<br>
                       NIP/NIDN/NUPTK/NIDN.
                       <br>
                       ..............................................
                       </font>
                   </td>
               </tr>
           </tbody>
       </table>
   </div>',
     'aktif' => true
   ]);

    \App\Models\TemplateSurat::create([
      'nama_template' => 'Surat Keterangan Lulus',
      'konten_template' => '<div class="container">
        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="width: 110px; text-align: left;">
                        <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/refs/heads/main/logo_unper.png" alt="Logo Universitas" style="width: 110px; height: 110px; display: block; margin: 0 auto;" onerror="this.onerror=null;this.src=\'https://placehold.co/110x110/cccccc/333333?text=Logo\';">
                    </td>
                    <td style="font-family: \'Times New Roman\', Times, serif; font-size: 13px; text-align: center;">
                        <center>
                            <font size="5"><b><span style="font-size: 14px;">YAYASAN UNIVERSITAS SILIWANGI</span></b></font><br>
                            <font size="4"><b><span style="font-size: 14px;">UNIVERSITAS PERJUANGAN TASIKMALAYA</span></b></font><br>
                            <font size="5"><b><span style="font-size: 14px;">FAKULTAS TEKNIK</span></b></font><br>
                            <font size="3"><i><span style="font-size: 11px;">Jalan Pembela Tanah Air (PETA) No. 177 Kota Tasikmalaya, Kode Pos 46115</span></i></font><br>
                            <font size="3"><i><span style="font-size: 11px;">Telepon (0265) 326058, laman : http://www.unper.ac.id</span></i></font>
                        </center>
                    </td>
                    <td style="width: 110px; text-align: right;">
                        <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/515b4b45417676e20e79f06fad8437d91c97c2cb/image.png" alt="Logo Yayasan" style="width: 110px; height: 110px; display: block; margin: 0 auto;" onerror="this.onerror=null;this.src=\'https://placehold.co/110x110/cccccc/333333?text=Logo\';">
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><hr style="border: 1px solid black;"></td>
                </tr>
            </tbody>
        </table>
        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td colspan="3" style="text-align: center;">
                        <font size="3"><b><u>SURAT KETERANGAN LULUS</u></b></font><br>
                        <font size="2"><u>Nomor : {{ $no_surat }}/SKL/FT-UP/{{ $data->bulan }}/{{ $data->tahun }}</u></font>
                    </td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Dengan ini menyatakan bahwa :</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="80"><font size="2">Nama</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">NIP/NIDN/NUPTK</font></td>
                    <td><font size="10">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Jabatan Akademik</font></td>
                    <td><font size="10">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Jabatan</font></td>
                    <td><font size="10">:</font></td>
                    <td><font size="2">Dekan</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Menerangkan dengan sesungguhnya bahwa :</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="80"><font size="2">Nama</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">NIM</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Jurusan/Prodi</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Fakultas</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">Teknik</font></td>
                </tr>
                <tr>
                    <td><font size="2">Jenjang</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">S - 1</font></td>
                </tr>
                <tr>
                    <td><font size="2">Status Awal Masuk</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Telah dinyatakan LULUS berdasarkan keputusan sidang yudisium pada :</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="80"><font size="2">Semester</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Tahun Akademik</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Tanggal Lulus / Yudisium</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">{{ $tanggal }} {{ $data->bulan }} {{ $data->tahun }}</font></td>
                </tr>
                <tr>
                    <td><font size="2">Nilai Skripsi</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">...........................</font></td>
                </tr>
                <tr>
                    <td><font size="2">IPK/SKS</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">_____ / ___</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Surat keterangan ini diberikan untuk dapat dipergunakan sebagaimana mestinya dan dibuat dalam proses penyelesaian dan berlaku sampai dengan tanggal diterbitkannya ijazah. Demikian surat keterangan ini dibuat, agar dapat dipergunakan sebagaimana mestinya.</font></td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="225" style="text-align: left; vertical-align: top;">
                        <font size="2">
                        &nbsp;
                        </font>
                    </td>
                    <td width="225" style="text-align: left; vertical-align: top;">
                        <font size="2">
                        Diterbitkan di Tasikmalaya<br>
                        Pada Tanggal {{ $tanggal }} {{ $data->bulan }}, {{ $data->tahun }}<br>
                        Dekan,
                        <br><br><br>
                        tanda tangan dan cap jabatan
                        <br><br><br>
                        NAMA PEJABAT<br>
                        NIP/NIDN/NUPTK/NIDN ___________
                        </font>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>',
      'aktif' => true
    ]);
    \App\Models\TemplateSurat::create([
      'nama_template' => 'Surat Keputusan Dekan',
      'konten_template' => '<center>
  <div style="text-align: center;">
    <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/refs/heads/main/logo_unper.png" width="90" height="90" style="display: block; margin-left: auto; margin-right: auto;">
    <div style="font-family: \'Times New Roman\', Times, serif; font-size: 16px; margin-top: 5px;">
      <strong>KEPUTUSAN</strong><br>
      <strong>DEKAN FAKULTAS TEKNIK UNIVERSITAS PERJUANGAN TASIKMALAYA</strong><br>
      <u>NOMOR</u> : {{ $data->noSurat }} / SK/Dek.FT-UP/{{ $data->bulan }}/{{ $data->tahun }}
    </div>
  </div>

  <br>

  <table width="600">
    <tbody><tr>
      <td style="text-align: center; font-weight: bold;">
        Tentang
      </td>
    </tr>
    <tr>
      <td style="text-align: center;">.............................................................</td>
    </tr>
    <tr>
      <td style="text-align: center;">.............................................................</td>
    </tr>
  </tbody></table>

  <br><br>

  <table width="600" style="font-family: \'Times New Roman\', Times, serif; font-size: 14px;">
    <tbody><tr>
      <td>Menimbang</td>
      <td>: a. bahwa ...............................................................</td>
    </tr>
    <tr>
      <td></td>
      <td>&nbsp;&nbsp;&nbsp; b. bahwa ...............................................................</td>
    </tr>
    <tr>
      <td>Mengingat</td>
      <td>: 1. bahwa ...............................................................</td>
    </tr>
  </tbody></table>

  <br>

  <table width="600">
    <tbody><tr>
      <td style="text-align: center; font-weight: bold;">
        MEMUTUSKAN
      </td>
    </tr>
  </tbody></table>

  <br>

  <table width="600" style="font-family: \'Times New Roman\', Times, serif; font-size: 14px;">
    <tbody><tr>
      <td style="width: 100px;">Menetapkan</td>
      <td>: ..................................................................................</td>
    </tr>
    <tr>
      <td>KESATU</td>
      <td>: ..................................................................................</td>
    </tr>
    <tr>
      <td>KEDUA</td>
      <td>: ..................................................................................</td>
    </tr>
    <tr>
      <td>KETIGA</td>
      <td>: ..................................................................................</td>
    </tr>
    <tr>
      <td>KE (dst)</td>
      <td>: Keputusan ini mulai berlaku pada tanggal ditetapkan dan apabila dikemudian hari <br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ternyata terdapat kekeliruan maka akan diadakan perbaikan sebagaimana mestinya.
      </td>
    </tr>
  </tbody></table>

  <br><br>

  <table width="600" style="font-family: \'Times New Roman\', Times, serif; font-size: 14px;">
    <tbody><tr>
      <td style="width: 300px;"></td>
      <td style="text-align: left;">Ditetapkan di Tasikmalaya</td>
    </tr>
    <tr>
      <td></td>
      <td style="text-align: left;">Pada tanggal {{ \$tanggal }}</td>
    </tr>
    <tr>
      <td></td>
      <td style="text-align: left;"><b>Dekan Fakultas Teknik<br>Universitas Perjuangan Tasikmalaya</b></td>
    </tr>
    <tr>
      <td></td>
      <td><br><br><br>Tanda tangan dan cap jabatan</td>
    </tr>
    <tr>
      <td></td>
      <td><u>NAMA PEJABAT</u><br>NIP/NIDN/NUPTK/NIDN : ................................</td>
    </tr>
  </tbody></table>
</center>',
      'aktif' => true
    ]);
  }
}
