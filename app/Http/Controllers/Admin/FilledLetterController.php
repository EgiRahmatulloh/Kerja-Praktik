<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilledLetter;
use App\Models\LetterQueue;
use App\Models\LetterType;
use App\Models\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\TemplateProcessor;

class FilledLetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Update template content for a specific letter
     */
    public function updateTemplate(Request $request, $id)
    {
        $letter = FilledLetter::with(['letterType', 'letterType.templateSurat'])
            ->findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'template_content' => 'required|string',
        ]);

        // Simpan konten template yang diedit ke session untuk digunakan saat preview/cetak
        session(['edited_template_' . $id => $validated['template_content']]);

        return redirect()->route('admin.filled-letters.show', $id)
            ->with('success', 'Template surat berhasil diperbarui. Perubahan akan diterapkan saat preview dan cetak surat.');
    }

    // Menampilkan daftar surat yang sudah diisi
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = FilledLetter::with(['user', 'letterType']);

        // Filter berdasarkan kepemilikan template atau pengaturan berbagi
        $adminUserIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();

        if ($user->sub_role) {
            $query->whereHas('letterType.templateSurat', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhere('share_setting', 'public')
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('share_setting', 'limited')
                            ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                                $q3->where('users.id', $user->id);
                            });
                    });
            })->whereNotIn('user_id', $adminUserIds); // Tambahkan filter ini juga untuk sub-admin
        } else {
            // Admin utama melihat semua surat, tetapi tidak menampilkan surat yang dibuat oleh pengguna dengan role 'admin'
            $query->whereNotIn('user_id', $adminUserIds);
        }

        // Filter berdasarkan status jika ada di request
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Jika tidak ada status yang diminta, tampilkan semua status kecuali 'rejected'
        else {
            $query->whereIn('status', ['pending', 'approved', 'printed', 'completed']);
        }

        // Filter berdasarkan jenis surat
        if ($request->filled('letter_type_id')) {
            $query->where('letter_type_id', $request->letter_type_id);
        }

        // Filter pencarian nama pemohon
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $letters = $query->latest()->paginate(10);

        // Ambil letter types yang sesuai dengan filter admin
        $letterTypeQuery = LetterType::query();
        if ($user->sub_role) {
            $letterTypeQuery->whereHas('templateSurat', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhere('share_setting', 'public')
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('share_setting', 'limited')
                            ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                                $q3->where('users.id', $user->id);
                            });
                    });
            });
        }
        $letterTypes = $letterTypeQuery->get();

        $selectedLetterType = $request->letter_type_id;
        $selectedStatus = $request->status;
        $searchQuery = $request->search;

        return view('admin.filled-letters.index', compact('letters', 'letterTypes', 'selectedLetterType', 'selectedStatus', 'searchQuery'));
    }

    // Menampilkan detail surat yang sudah diisi
    // Pada method show(), tambahkan pengecekan sebelum melakukan foreach
    public function show($id)
    {
        $letter = FilledLetter::with(['user', 'letterType', 'letterType.templateSurat'])
            ->findOrFail($id);

        // Membuat preview surat
        $template = $letter->letterType->templateSurat;

        // Cek apakah ada template yang diedit di session
        $content = session('edited_template_' . $id) ?? $template->html_content;

        // Gunakan ini untuk logging yang aman
        Log::info('Admin Filled Data:', is_array($letter->filled_data) ? $letter->filled_data : ['data' => $letter->filled_data]);

        // Pastikan filled_data adalah array sebelum melakukan foreach
        if (is_array($letter->filled_data)) {
            // Ganti variabel dengan data yang diisi
            foreach ($letter->filled_data as $key => $value) {
                $content = str_replace('{{ $' . $key . ' }}', $value, $content);
                // Tambahkan penggantian untuk format $data->key
                $content = str_replace("{{ \$data->" . $key . " }}", $value, $content);
                $content = str_replace("{{\$data->" . $key . "}}", $value, $content);
            }
        } else {
            // Log error jika filled_data bukan array
            Log::error('filled_data bukan array:', ['filled_data' => $letter->filled_data]);

            // Konversi filled_data dari string ke array jika perlu
            if (is_string($letter->filled_data)) {
                $letter->filled_data = json_decode($letter->filled_data, true) ?? [];
            } else {
                // Jika bukan string dan bukan array, inisialisasi sebagai array kosong
                $letter->filled_data = [];
            }
        }

        // Definisikan variabel $preview dengan nilai $content
        $preview = $content;

        // Tambahkan return view dengan variabel $preview
        return view('admin.filled-letters.show', compact('letter', 'content', 'preview'));
    }

    // Menampilkan form untuk mengedit surat yang sudah diisi
    public function edit($id)
    {
        $letter = FilledLetter::with(['letterType'])->findOrFail($id);

        // Pastikan filled_data adalah array
        if (!is_array($letter->filled_data)) {
            // Jika bukan array, konversi dari JSON string ke array
            if (is_string($letter->filled_data)) {
                $letter->filled_data = json_decode($letter->filled_data, true) ?? [];
            } else {
                // Jika bukan string dan bukan array, inisialisasi sebagai array kosong
                $letter->filled_data = [];
            }

            // Log untuk debugging
            Log::warning('filled_data dikonversi dari string ke array', [
                'letter_id' => $id,
                'filled_data' => $letter->filled_data
            ]);
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $letterTypeQuery = LetterType::query();

        if ($user->sub_role) {
            $letterTypeQuery->whereHas('templateSurat', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhere('share_setting', 'public')
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('share_setting', 'limited')
                            ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                                $q3->where('users.id', $user->id);
                            });
                    });
            });
        }
        $letterTypes = $letterTypeQuery->get();

        return view('admin.filled-letters.edit', compact('letter', 'letterTypes'));
    }

    // Menyimpan perubahan surat yang sudah diisi
    public function update(Request $request, $id)
    {
        $letter = FilledLetter::findOrFail($id);

        $validated = $request->validate([
            'no_surat' => 'nullable|string|max:100',
            'catatan_admin' => 'nullable|string',
            'status' => 'required|in:pending,approved,dicetak'
        ]);

        // Update data surat jika ada
        if ($request->has('data')) {
            $letter->filled_data = $request->data;
        }

        // Jika status diubah menjadi pending, hapus dari antrian
        if ($validated['status'] == 'pending' && $letter->status == 'approved') {
            // Cari dan hapus antrian yang terkait dengan surat ini
            $queue = LetterQueue::where('filled_letter_id', $letter->id)->first();
            if ($queue) {
                $queue->delete();
            }
        }

        $letter->update($validated);

        return redirect()->route('admin.filled-letters.index')
            ->with('success', 'Surat berhasil diperbarui.');
    }

    // Memperbarui status surat
    public function updateStatus(Request $request, $id)
    {
        try {
            $letter = FilledLetter::findOrFail($id);

            // Validasi dasar
            $rules = [
                'status' => 'required|in:pending,approved,rejected,printed',
                'no_surat' => 'nullable|string',
            ];

            // Tambahkan validasi wajib untuk catatan_admin jika status rejected
            if ($request->input('status') === 'rejected') {
                $rules['catatan_admin'] = 'required|string';
            } else {
                $rules['catatan_admin'] = 'nullable|string';
            }

            $validated = $request->validate($rules, [
                'catatan_admin.required' => 'Catatan admin wajib diisi jika status ditolak'
            ]);

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info('Update Status Request:', [
                'letter_id' => $id,
                'current_status' => $letter->status,
                'new_status' => $validated['status'],
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);

            // Set properti model secara langsung
            $letter->status = $validated['status'];
            $letter->catatan_admin = $validated['catatan_admin'] ?? null;

            // Update nomor surat jika ada
            if (!empty($validated['no_surat'])) {
                $letter->no_surat = $validated['no_surat'];
            }

            // Jika status diubah menjadi pending atau rejected, hapus dari antrian
            if (($validated['status'] == 'pending' || $validated['status'] == 'rejected')) {
                // Cari dan hapus antrian yang terkait dengan surat ini
                $queue = LetterQueue::where('filled_letter_id', $letter->id)->first();
                if ($queue) {
                    $queue->delete();
                    // Log penghapusan antrian
                    \Illuminate\Support\Facades\Log::info("Menghapus antrian #{$queue->id} karena status surat #{$letter->id} diubah menjadi {$validated['status']}");
                }
            }

            // Update surat dan log hasilnya
            $saved = $letter->save();
            \Illuminate\Support\Facades\Log::info('Status surat berhasil diperbarui (force dirty):', [
                'letter_id' => $letter->id,
                'new_status' => $letter->status,
                'catatan_admin' => $letter->catatan_admin,
                'saved_result' => $saved
            ]);

            // Cek apakah request adalah AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status surat berhasil diperbarui.',
                    'new_status' => $letter->status,
                    'catatan_admin' => $letter->catatan_admin
                ]);
            }

            return redirect()->route('admin.filled-letters.index')
                ->with('success', 'Status surat berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating status:', [
                'letter_id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.filled-letters.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui status.');
        }
    }

    // Menampilkan preview surat dalam bentuk PDF
    public function print($id)
    {
        $letter = FilledLetter::with(['user', 'letterType', 'letterType.templateSurat'])
            ->findOrFail($id);

        $template = $letter->letterType->templateSurat;

        // Cek apakah ada template yang diedit di session
        $content = session('edited_template_' . $id) ?? $template->konten_template;

        // Proses template secara manual
        $renderedContent = $content;

        // Ganti semua variabel dengan nilai sebenarnya
        foreach ($letter->filled_data as $key => $value) {
            $renderedContent = str_replace("{{ \$" . $key . " }}", $value, $renderedContent);
            $renderedContent = str_replace("{{\$" . $key . "}}", $value, $renderedContent);
            $renderedContent = str_replace("{{ \$data->" . $key . " }}", $value, $renderedContent);
            $renderedContent = str_replace("{{\$data->" . $key . "}}", $value, $renderedContent);
        }

        $renderedContent = str_replace("{{ \$noSurat }}", $letter->no_surat, $renderedContent);
        $renderedContent = str_replace("{{\$noSurat}}", $letter->no_surat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->noSurat }}", $letter->no_surat, $renderedContent);
        $renderedContent = str_replace("{{\$data->noSurat}}", $letter->no_surat, $renderedContent);


        // Ganti variabel tanggal surat
        $namaBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $day = date('d');
        $monthNum = date('m');
        $year = date('Y');
        $bulanHuruf = $namaBulan[$monthNum];

        $tglSurat = "$day $bulanHuruf $year";
        if (isset($letter->filled_data['tglSurat'])) {
            $timestamp = strtotime($letter->filled_data['tglSurat']);
            $day = date('d', $timestamp);
            $monthNum = date('m', $timestamp);
            $year = date('Y', $timestamp);
            $bulanHuruf = $namaBulan[$monthNum];
            $tglSurat = "$day $bulanHuruf $year";
        }
        $renderedContent = str_replace("{{ \$tglSurat }}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{\$tglSurat}}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->tglSurat }}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{\$data->tglSurat}}", $tglSurat, $renderedContent);

        // Tambahan untuk format tanggal yang lebih kompleks
        $namaBulanSingkat = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        ];
        $timestampFormatted = strtotime($tglSurat);
        $dayFormatted = date('d', $timestampFormatted);
        $monthNumFormatted = date('m', $timestampFormatted);
        $yearFormatted = date('Y', $timestampFormatted);
        $bulanSingkat = $namaBulanSingkat[$monthNumFormatted];
        $formattedDate = "$dayFormatted $bulanSingkat $yearFormatted";
        $renderedContent = str_replace("{{ date('d M Y', strtotime(\$data->tglSurat)); }}", $formattedDate, $renderedContent);
        $renderedContent = str_replace("{{ date('d M\nY', strtotime(\$data-\n>tglSurat)); }}", $formattedDate, $renderedContent);

        // Ganti variabel bulan dan tahun
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Ganti placeholder bulan
        $renderedContent = str_replace("{{ \$data->bulan }}", $currentMonth, $renderedContent);
        $renderedContent = str_replace("{{{ \$data->bulan }}}", $currentMonth, $renderedContent);
        $renderedContent = str_replace("{{\$data->bulan}}", $currentMonth, $renderedContent);
        $renderedContent = str_replace("{{ \$bulan }}", $currentMonth, $renderedContent);
        $renderedContent = str_replace("{{{ \$bulan }}}", $currentMonth, $renderedContent);
        $renderedContent = str_replace("{{\$bulan}}", $currentMonth, $renderedContent);

        // Ganti placeholder tahun
        $renderedContent = str_replace("{{ \$data->tahun }}", $currentYear, $renderedContent);
        $renderedContent = str_replace("{{{ \$data->tahun }}}", $currentYear, $renderedContent);
        $renderedContent = str_replace("{{\$data->tahun}}", $currentYear, $renderedContent);
        $renderedContent = str_replace("{{ \$tahun }}", $currentYear, $renderedContent);
        $renderedContent = str_replace("{{{ \$tahun }}}", $currentYear, $renderedContent);
        $renderedContent = str_replace("{{\$tahun}}", $currentYear, $renderedContent);

        // Ganti format gabungan /bulan/tahun
        $renderedContent = str_replace("/{{ \$data->bulan }}/{{ \$data->tahun }}", "/$currentMonth/$currentYear", $renderedContent);
        $renderedContent = str_replace("/{{\$data->bulan}}/{{\$data->tahun}}", "/$currentMonth/$currentYear", $renderedContent);
        $renderedContent = str_replace("/{{ \$bulan }}/{{ \$tahun }}", "/$currentMonth/$currentYear", $renderedContent);
        $renderedContent = str_replace("/{{\$bulan}}/{{\$tahun}}", "/$currentMonth/$currentYear", $renderedContent);

        // Ganti variabel ttd dan namaTtd
        if (isset($letter->filled_data['ttd'])) {
            $renderedContent = str_replace("{{ \$ttd }}", $letter->filled_data['ttd'], $renderedContent);
            $renderedContent = str_replace("{{\$ttd}}", $letter->filled_data['ttd'], $renderedContent);
            $renderedContent = str_replace("{{ \$data->ttd }}", $letter->filled_data['ttd'], $renderedContent);
            $renderedContent = str_replace("{{\$data->ttd}}", $letter->filled_data['ttd'], $renderedContent);
        }

        if (isset($letter->filled_data['namaTtd'])) {
            $renderedContent = str_replace("{{ \$namaTtd }}", $letter->filled_data['namaTtd'], $renderedContent);
            $renderedContent = str_replace("{{\$namaTtd}}", $letter->filled_data['namaTtd'], $renderedContent);
            $renderedContent = str_replace("{{ \$data->namaTtd }}", $letter->filled_data['namaTtd'], $renderedContent);
            $renderedContent = str_replace("{{\$data->namaTtd}}", $letter->filled_data['namaTtd'], $renderedContent);
        }

        // Ganti tag Blade untuk konten HTML
        foreach ($letter->filled_data as $key => $value) {
            $renderedContent = str_replace("{!! \$data->" . $key . " !!}", $value, $renderedContent);
            $renderedContent = str_replace("{!!\$data->" . $key . "!!}", $value, $renderedContent);
        }

        // Update status surat menjadi printed jika belum
        if ($letter->status == 'approved') {
            $letter->update(['status' => 'printed']);
        }

        // Hapus template yang diedit dari session setelah dicetak
        session()->forget('edited_template_' . $id);

        return $this->generateDocx($id);
    }

    // Menghasilkan file DOCX dari surat
    public function generateDocx($id)
    {
        $letter = FilledLetter::with(['user', 'letterType', 'letterType.templateSurat'])
            ->findOrFail($id);

        $template = $letter->letterType->templateSurat;

        // Ambil path file template DOCX
        $templatePath = $template->full_path;

        // Pastikan file template ada
        if (!file_exists($templatePath)) {
            abort(404, 'Template file tidak ditemukan');
        }

        // Baca template DOCX yang sudah ada
        $templateProcessor = new TemplateProcessor($templatePath);

        // Ganti semua variabel dengan nilai sebenarnya menggunakan TemplateProcessor
        foreach ($letter->filled_data as $key => $value) {
            // Format variabel untuk PHPWord template processor
            $templateProcessor->setValue($key, $value);
            $templateProcessor->setValue('data.' . $key, $value);
        }

        // Format nomor surat
        $formattedNoSurat = $letter->no_surat;
        $templateProcessor->setValue('noSurat', $formattedNoSurat);
        $templateProcessor->setValue('data.noSurat', $formattedNoSurat);

        // Ganti variabel tanggal surat
        $namaBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $day = date('d');
        $monthNum = date('m');
        $year = date('Y');
        $bulanHuruf = $namaBulan[$monthNum];

        $tglSurat = "$day $bulanHuruf $year";
        if (isset($letter->filled_data['tglSurat'])) {
            $timestamp = strtotime($letter->filled_data['tglSurat']);
            $day = date('d', $timestamp);
            $monthNum = date('m', $timestamp);
            $year = date('Y', $timestamp);
            $bulanHuruf = $namaBulan[$monthNum];
            $tglSurat = "$day $bulanHuruf $year";
        }
        $templateProcessor->setValue('tglSurat', $tglSurat);
        $templateProcessor->setValue('data.tglSurat', $tglSurat);

        // Format tanggal yang lebih kompleks
        $namaBulanSingkat = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        ];
        $timestampFormatted = strtotime($tglSurat);
        $dayFormatted = date('d', $timestampFormatted);
        $monthNumFormatted = date('m', $timestampFormatted);
        $yearFormatted = date('Y', $timestampFormatted);
        $bulanSingkat = $namaBulanSingkat[$monthNumFormatted];
        $formattedDate = "$dayFormatted $bulanSingkat $yearFormatted";
        $templateProcessor->setValue('formattedDate', $formattedDate);
        $templateProcessor->setValue('data.formattedDate', $formattedDate);

        // Ganti variabel bulan dan tahun
        // Bulan dalam format angka
        $templateProcessor->setValue('bulan', $monthNum);
        $templateProcessor->setValue('data.bulan', $monthNum);

        // Bulan dalam format huruf
        $templateProcessor->setValue('bulanHuruf', $bulanHuruf);
        $templateProcessor->setValue('data.bulanHuruf', $bulanHuruf);

        // Tahun
        $templateProcessor->setValue('tahun', $year);
        $templateProcessor->setValue('data.tahun', $year);

        // Ganti variabel ttd dan namaTtd
        if (isset($letter->filled_data['ttd'])) {
            $templateProcessor->setValue('ttd', $letter->filled_data['ttd']);
            $templateProcessor->setValue('data.ttd', $letter->filled_data['ttd']);
        }

        if (isset($letter->filled_data['namaTtd'])) {
            $templateProcessor->setValue('namaTtd', $letter->filled_data['namaTtd']);
            $templateProcessor->setValue('data.namaTtd', $letter->filled_data['namaTtd']);
        }

        Log::info('Before status update in generateDocx:', ['letter_id' => $letter->id, 'current_status' => $letter->status]);

        // Update status surat menjadi printed jika belum
        if ($letter->status == 'approved' || $letter->status == 'completed') {
            $letter->update(['status' => 'printed']);
            Log::info('Status updated to printed in generateDocx:', ['letter_id' => $letter->id, 'new_status' => $letter->status]);
        } else {
            Log::info('Status not updated in generateDocx (not approved or completed):', ['letter_id' => $letter->id, 'current_status' => $letter->status]);
        }

        // Simpan hasil template yang sudah diproses
        // Buat nama file yang unik dengan nama user dan tanggal
        $userName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $letter->user->name);
        $letterTypeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $letter->letterType->nama_jenis);
        $currentDate = date('Y-m-d');

        $fileName = $letterTypeName . '_' . $userName . '_' . $currentDate . '_' . $letter->id . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');

        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
