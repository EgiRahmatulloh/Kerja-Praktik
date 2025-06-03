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
        $query = FilledLetter::with(['user', 'letterType']);

        // Filter berdasarkan jenis surat
        if ($request->filled('letter_type_id')) {
            $query->where('letter_type_id', $request->letter_type_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $letters = $query->latest()->paginate(10);

        $letterTypes = LetterType::all();
        $selectedLetterType = $request->letter_type_id;
        $selectedStatus = $request->status;

        return view('admin.filled-letters.index', compact('letters', 'letterTypes', 'selectedLetterType', 'selectedStatus'));
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

        $letterTypes = LetterType::all();
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
        $letter = FilledLetter::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,printed',
            'no_surat' => 'nullable|string',
            'catatan_admin' => 'nullable|string',
        ]);

        // Jika status diubah menjadi rejected, pastikan catatan admin diisi
        if ($validated['status'] == 'rejected' && empty($validated['catatan_admin'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['catatan_admin' => 'Catatan admin wajib diisi jika status ditolak']);
        }

        $updateData = [
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'no_surat' => $validated['no_surat'] ?? $letter->no_surat,
        ];

        // Jika status diubah menjadi pending atau rejected, hapus dari antrian
        if (($validated['status'] == 'pending' || $validated['status'] == 'rejected')) {
            // Cari dan hapus antrian yang terkait dengan surat ini
            // Pastikan mencari berdasarkan filled_letter_id untuk menghindari masalah dengan relasi
            $queue = LetterQueue::where('filled_letter_id', $letter->id)->first();
            if ($queue) {
                $queue->delete();
                // Log penghapusan antrian
                Log::info("Menghapus antrian surat #{$letter->id} karena status diubah menjadi {$validated['status']}");
            }
        }

        // Jika status diubah menjadi approved, masukkan ke antrian
        if ($validated['status'] == 'approved') {
            // Ambil jenis surat
            $letterType = $letter->letterType;

            // Tambahkan surat ke antrian jika statusnya diubah menjadi approved
            // dan belum ada di antrian
            if (!$letter->queue) {
                // Cari jadwal pelayanan yang aktif
                $serviceSchedule = ServiceSchedule::where('is_active', true)->first();

                if (!$serviceSchedule) {
                    // Jika tidak ada jadwal pelayanan, gunakan default (1 hari dari sekarang)
                    $scheduledDate = now()->addDay();
                } else {
                    // Cari antrian terakhir untuk menentukan jadwal berikutnya
                    $lastQueue = LetterQueue::where('status', 'waiting')
                        ->orderBy('scheduled_date', 'desc')
                        ->first();

                    if (!$lastQueue) {
                        // Jika belum ada antrian, jadwalkan di awal jam pelayanan hari ini atau besok
                        $today = Carbon::today();
                        $now = Carbon::now();
                        $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($today);

                        // Jika sekarang sudah melewati jam mulai hari ini, jadwalkan besok
                        if ($now->gt($startTime)) {
                            $tomorrow = Carbon::tomorrow();
                            $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($tomorrow);
                        }

                        $scheduledDate = $startTime;
                    } else {
                        // Jadwalkan sesuai waktu proses setelah antrian terakhir
                        $scheduledDate = Carbon::parse($lastQueue->scheduled_date)->addMinutes($serviceSchedule->processing_time);

                        // Pastikan masih dalam jam pelayanan
                        $scheduleDate = $scheduledDate->format('Y-m-d');
                        $startTime = Carbon::parse($serviceSchedule->start_time)->setDateFrom($scheduleDate);
                        $endTime = Carbon::parse($serviceSchedule->end_time)->setDateFrom($scheduleDate);

                        // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
                        if ($scheduledDate->gt($endTime)) {
                            $nextDay = Carbon::parse($scheduleDate)->addDay();
                            $scheduledDate = Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextDay);
                        }

                        // Jika jadwal sebelum jam mulai pelayanan, pindahkan ke jam mulai pelayanan
                        if ($scheduledDate->lt($startTime)) {
                            $scheduledDate = $startTime;
                        }
                    }
                }

                LetterQueue::create([
                    'filled_letter_id' => $letter->id,
                    'scheduled_date' => $scheduledDate,
                    'status' => 'waiting'
                ]);
            }
        }

        $letter->update($updateData);

        return redirect()->route('admin.filled-letters.show', $letter->id)
            ->with('success', 'Status surat berhasil diperbarui.');
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

        // Ganti variabel nomor surat dan kode surat
        $renderedContent = str_replace("{{ \$noSurat }}", $letter->no_surat, $renderedContent);
        $renderedContent = str_replace("{{\$noSurat}}", $letter->no_surat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->noSurat }}", $letter->no_surat, $renderedContent);
        $renderedContent = str_replace("{{\$data->noSurat}}", $letter->no_surat, $renderedContent);

        $renderedContent = str_replace("{{ \$kodeSurat }}", $letter->kode_surat, $renderedContent);
        $renderedContent = str_replace("{{\$kodeSurat}}", $letter->kode_surat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->kodeSurat }}", $letter->kode_surat, $renderedContent);
        $renderedContent = str_replace("{{\$data->kodeSurat}}", $letter->kode_surat, $renderedContent);

        // Ganti variabel tanggal surat
        $tglSurat = date('Y-m-d');
        if (isset($letter->filled_data['tglSurat'])) {
            $tglSurat = $letter->filled_data['tglSurat'];
        }
        $renderedContent = str_replace("{{ \$tglSurat }}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{\$tglSurat}}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->tglSurat }}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{\$data->tglSurat}}", $tglSurat, $renderedContent);

        // Tambahan untuk format tanggal yang lebih kompleks
        $formattedDate = date('d M Y', strtotime($tglSurat));
        $renderedContent = str_replace("{{ date('d M Y', strtotime(\$data->tglSurat)); }}", $formattedDate, $renderedContent);
        $renderedContent = str_replace("{{ date('d M\nY', strtotime(\$data-\n>tglSurat)); }}", $formattedDate, $renderedContent);

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

        return view('print.letter', compact('letter', 'renderedContent'));
    }

    // Menghasilkan file PDF dari surat
    public function generatePdf($id)
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

        // Ganti variabel nomor surat dan kode surat
        // Ganti variabel nomor surat dan kode surat
        // Format nomor surat: NoUrut saja
        $formattedNoSurat = $letter->no_surat;

        $renderedContent = str_replace("{{ \$noSurat }}", $formattedNoSurat, $renderedContent);
        $renderedContent = str_replace("{{\$noSurat}}", $formattedNoSurat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->noSurat }}", $formattedNoSurat, $renderedContent);
        $renderedContent = str_replace("{{\$data->noSurat}}", $formattedNoSurat, $renderedContent);

        $renderedContent = str_replace("{{ \$kodeSurat }}", $letter->kode_surat, $renderedContent);
        $renderedContent = str_replace("{{\$kodeSurat}}", $letter->kode_surat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->kodeSurat }}", $letter->kode_surat, $renderedContent);
        $renderedContent = str_replace("{{\$data->kodeSurat}}", $letter->kode_surat, $renderedContent);

        // Ganti variabel tanggal surat
        $tglSurat = date('Y-m-d');
        if (isset($letter->filled_data['tglSurat'])) {
            $tglSurat = $letter->filled_data['tglSurat'];
        }
        $renderedContent = str_replace("{{ \$tglSurat }}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{\$tglSurat}}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{ \$data->tglSurat }}", $tglSurat, $renderedContent);
        $renderedContent = str_replace("{{\$data->tglSurat}}", $tglSurat, $renderedContent);

        // Tambahan untuk format tanggal yang lebih kompleks
        $formattedDate = date('d M Y', strtotime($tglSurat));
        $renderedContent = str_replace("{{ date('d M Y', strtotime(\$data->tglSurat)); }}", $formattedDate, $renderedContent);
        $renderedContent = str_replace("{{ date('d M\nY', strtotime(\$data-\n>tglSurat)); }}", $formattedDate, $renderedContent);

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

        $pdf = Pdf::loadView('print.letter_pdf', ['letter' => $letter, 'renderedContent' => $renderedContent]);
        return $pdf->stream('surat_' . $letter->letterType->name . '_' . $letter->id . '.pdf');
    }
}
