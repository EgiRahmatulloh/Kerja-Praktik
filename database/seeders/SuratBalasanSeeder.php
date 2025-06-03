<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateSurat;

class SuratBalasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TemplateSurat::create([
            'nama_template' => 'Surat Balasan',
            'konten_template' => '<center>
    <table width="450" style="border-collapse: collapse;">
        <tbody><tr>
            <td style="width: 110px; text-align: left;">
                <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/refs/heads/main/logo_unper.png" alt="Logo Universitas" style="width: 110px; height: 110px; display: block; margin: 0 auto;" onerror="this.onerror=null;this.src=\'https://placehold.co/110x110/cccccc/333333?text=Logo\';">
            </td>
            <td style="font-family: \'Times New Roman\', Times, serif; font-size: 13px; text-align: center;">
                <center>
                    <font size="5"><b><span style="font-size: 14px;">YAYASAN UNIVERSITAS SILIWANGI</span></b></font><br>
                    <font size="4"><b><span style="font-size: 14px;">UNIVERSITAS PERJUANGAN TASIKMALAYA</span></b></font><br>
                    <font size="5"><b><span style="font-size: 14px;">FAKULTAS TEKNIK</span></b></font><br>
                    <font size="3"><i><span style="font-size: 11px;">Jalan Pembela Tanah Air (PETA</span><span style="font-size: 11px;">) No. 177</span> <span style="font-size: 11px;">Kota Tasikmalaya, Kode Pos 46115</span></i></font><br>
                    <font size="3"><i><span style="font-size: 11px;">Telepon (0265) 326058, laman :</span> <span style="font-size: 11px;">http://www.unper.ac.id</span></i></font>
                </center>
            </td>
            <td style="width: 110px; text-align: right;">
                <img src="https://raw.githubusercontent.com/EgiRahmatulloh/Logo/515b4b45417676e20e79f06fad8437d91c97c2cb/image.png" alt="Logo Yayasan" style="width: 110px; height: 110px; display: block; margin: 0 auto;" onerror="this.onerror=null;this.src=\'https://placehold.co/110x110/cccccc/333333?text=Logo\';">
            </td>
        </tr>
        <tr>
            <td colspan="3"><hr style="border: 1px solid black;"></td>
        </tr>
    </tbody></table>
    <br>

    <table width="450" style="border-collapse: collapse; margin-top: 10px;">
        <tbody>
            <tr>
                <td colspan="3" style="text-align: right;"><font size="2">Tempat, tanggal ...............................</font></td>
            </tr>
        </tbody>
    </table>

    <br>

    <table width="450" style="border-collapse: collapse;">
        <tbody>
            <tr>
                <td width="70"><font size="2">Nomor</font></td>
                <td width="10"><font size="2">:</font></td>
                <td><font size="2">....../SPm/FT-UP/bulan......./tahun.......</font></td>
            </tr>
            <tr>
                <td><font size="2">Lampiran</font></td>
                <td><font size="2">:</font></td>
                <td><font size="2">........................................................</font></td>
            </tr>
            <tr>
                <td><font size="2">Perihal</font></td>
                <td><font size="2">:</font></td>
                <td><font size="2">........................................................</font></td>
            </tr>
        </tbody>
    </table>

    <br>

    <table width="450" style="border-collapse: collapse;">
        <tbody>
            <tr>
                <td width="70"><font size="2">Kepada Yth</font></td>
                <td width="10"><font size="2">:</font></td>
                <td><font size="2">........................................................</font></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td><font size="2">Universitas Perjuangan Tasikmalaya</font></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td><font size="2">Di tempat</font></td>
            </tr>
        </tbody>
    </table>

    <br>

    <table width="450" style="border-collapse: collapse;">
        <tbody>
            <tr>
                <td width="80"></td>
                <td colspan="2">
                    <font size="2">Dengan Hormat,</font>
                </td>
            </tr>
            <tr>
                <td width="80"></td>
                <td colspan="2">
                    <font size="2">
                    <br>
                    Sehubungan dengan adanya Surat Edaran Nomor .............................., mengenai <br>
                    .................................................................................................................................................................................................
                    <br><br>
                    Maka ......................................................................................................................................................................................
                    pemberitahuan tersebut kepada ................................................................ dengan data terlampir.
                    <br><br>
                    Terimakasih atas perhatian Bapak/Ibu. Kami ucapkan terimakasih.
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
                    <strong>NAMA PEJABAT</strong><br>
                    NIP/NIDN/NUPTK/NIDN.
                    <br>
                    ..............................................
                    </font>
                </td>
            </tr>
        </tbody>
    </table>
</center>',
            'aktif' => true
        ]);
    }
}