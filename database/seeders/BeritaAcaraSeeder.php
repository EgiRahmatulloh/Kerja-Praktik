<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateSurat;

class BeritaAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TemplateSurat::create([
            'nama_template' => 'Berita Acara',
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
                        <font size="3"><b>BERITA ACARA</b></font><br>
                        <font size="2"><u>Nomor : ......./BA/FT-UP/............</u></font>
                    </td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Pada hari ini, .............<u>tanggal</u>........., bulan .....<u>tahun</u>......, kami masing - masing:</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="20"><font size="2">1.</font></td>
                    <td width="50"><font size="2"><u>Nama Pejabat</u></font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">.......<u>NIP/NIDN/NUPTK</u> dan <u>Jabatan</u>., selanjutnya disebut Pihak</font></td>
                </tr>
                <tr>
                    <td></td>
                    <td><font size="2">Pertama dan</font></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="20"><font size="2">2.</font></td>
                    <td><font size="2">.......<u>untuk ini</u>....................................... ., selanjutnya disebut Pihak Kedua, telah</font></td>
                </tr>
                <tr>
                    <td></td>
                    <td><font size="2">melaksanakan</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="20"><font size="2">1.</font></td>
                    <td><font size="2">........................................................................................................................................................................................</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="20"><font size="2">2.</font></td>
                    <td><font size="2">dan seterusnya</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Berita acara ini dibuat dengan sebenarnya berdasarkan .............................</font></td>
                </tr>
            </tbody>
        </table><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="225" style="text-align: left; vertical-align: top;">
                        <font size="2">
                        Pihak Pertama,
                        <br><br><br>
                        tanda tangan
                        <br><br><br>
                        Nama Lengkap
                        <br><br>
                        Dibuat di ................................
                        <br><br>
                        Pihak Kedua,
                        <br><br><br>
                        tanda tangan
                        <br><br><br>
                        Nama Lengkap
                        </font>
                    </td>
                    <td width="225" style="text-align: left; vertical-align: top;">
                        <font size="2">
                        <br><br><br><br><br><br>
                        Mengetahui/Mengesahkan<br>
                        Nama Jabatan
                        <br><br><br>
                        tanda tangan
                        <br><br><br>
                        Nama Lengkap
                        </font>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>',
            'aktif' => true
        ]);
    }
}