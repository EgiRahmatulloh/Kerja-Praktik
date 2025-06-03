<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateSurat;

class SuratKuasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TemplateSurat::create([
            'nama_template' => 'Surat Kuasa',
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
                        <font size="3"><b>SURAT KUASA</b></font><br>
                        <font size="2">Nomor : ......./SKu/FT-UP/............</font>
                    </td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td colspan="3"><font size="2">Yang bertanda tangan di bawah ini,</font></td>
                </tr>
                <tr>
                    <td width="70"><font size="2">Nama</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">........................................................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Jabatan</font></td>
                    <td><font size="2">:</font></td>
                    <td><font size="2">........................................................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Alamat</font></td>
                    <td><font size="2">:</font></td>
                    <td><font size="2">........................................................</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td colspan="3"><font size="2">dengan ini memberikan kuasa kepada,</font></td>
                </tr>
                <tr>
                    <td width="70"><font size="2">Nama</font></td>
                    <td width="10"><font size="2">:</font></td>
                    <td><font size="2">........................................................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Jabatan</font></td>
                    <td><font size="2">:</font></td>
                    <td><font size="2">........................................................</font></td>
                </tr>
                <tr>
                    <td><font size="2">Alamat</font></td>
                    <td><font size="2">:</font></td>
                    <td><font size="2">........................................................</font></td>
                </tr>
            </tbody>
        </table>

        <br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">untuk ............................................................................................................................................................................................................</font></td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td><font size="2">Surat kuasa ini dibuat untuk dipergunakan sebagaimana mestinya.</font></td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table width="450" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td width="225" style="text-align: left; vertical-align: top;">
                        <font size="2">
                        <u>Tanggal</u> ......................
                        <br><br>
                        Penerima Kuasa,
                        <br><br><br><br>
                        tanda tangan
                        <br><br><br>
                        Nama Penerima Kuasa<br>
                        NIP/NIDN/NUPTK<br>
                        ..................................
                        </font>
                    </td>
                    <td width="225" style="text-align: left; vertical-align: top;">
                        <font size="2">
                        Penerima Kuasa,
                        <br><br><br>
                        tanda tangan materai dan<br>
                        cap jabatan atau cap dinas
                        <br><br><br>
                        Nama Penerima Kuasa<br>
                        NIP/NIDN/NUPTK<br>
                        ..................................
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