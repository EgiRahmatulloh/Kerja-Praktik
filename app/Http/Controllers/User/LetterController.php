<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Events\NewLetterSubmitted;
use App\Models\FilledLetter;
use App\Models\LetterType;
use Illuminate\Http\Request;
use App\Models\TemplateSurat;
use Illuminate\Support\Facades\Auth;

class LetterController extends Controller
{
    /**
     * Menampilkan daftar jenis surat yang tersedia
     */
    public function index()
    {
        $letterTypes = LetterType::where('is_public', true)->get();
        return view('user.letters.index', compact('letterTypes'));
    }

    /**
     * Menampilkan form untuk mengisi surat
     */
    public function create($id)
    {
        $letterType = LetterType::with('dataItems')->where('is_public', true)->findOrFail($id);
        return view('user.letters.create', compact('letterType'));
    }

    /**
     * Menyimpan data surat yang diisi
     */
    public function store(Request $request, $id)
    {
        $letterType = LetterType::with('templateSurat')->where('is_public', true)->findOrFail($id);

        // Validasi input berdasarkan data items yang diperlukan
        $rules = [];
        foreach ($letterType->dataItems as $item) {
            $rules[$item->key] = $item->required ? 'required' : 'nullable';
        }

        $validatedData = $request->validate($rules);

        // Pastikan templateSurat tidak null
        if (!$letterType->templateSurat) {
            return redirect()->back()->with('error', 'Template surat tidak ditemukan untuk jenis surat ini.');
        }

        // Memastikan awal kalimat menggunakan huruf kapital
        foreach ($validatedData as $key => $value) {
            if (is_string($value) && strlen($value) > 0) {
                $validatedData[$key] = ucfirst($value);
            }
        }

        // Cari nomor terakhir untuk jenis surat ini
        $lastLetter = FilledLetter::where('letter_type_id', $letterType->id)
            ->orderBy('no_surat', 'desc')
            ->first();

        if ($lastLetter) {
            // Increment nomor terakhir
            $nextNumber = intval($lastLetter->no_surat) + 1;
        } else {
            // Jika belum ada surat dengan kode ini, mulai dari 1
            $nextNumber = 1;
        }

        // Update last_number di letterType
        $letterType->last_number = $nextNumber;
        $letterType->save();

        // Set nomor surat dengan format 3 digit
        $noSurat = sprintf('%03d', $nextNumber);

        // Simpan data surat
        $filledLetter = new FilledLetter();
        $filledLetter->user_id = Auth::id();
        $filledLetter->letter_type_id = $letterType->id;
        $filledLetter->filled_data = json_encode($validatedData);
        $filledLetter->status = 'pending';
        $filledLetter->no_surat = $noSurat;

        $filledLetter->save();

        // Trigger event untuk notifikasi real-time
        event(new \App\Events\NewLetterSubmitted($filledLetter));

        return redirect()->route('user.letters.history')
            ->with('success', 'Surat berhasil diajukan dan sedang menunggu persetujuan admin.');
    }

    /**
     * Menampilkan riwayat surat yang pernah diajukan
     */
    public function history()
    {
        $letters = FilledLetter::with('letterType')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.letters.history', compact('letters'));
    }

    /**
     * Menampilkan detail surat
     */
    public function show($id)
    {
        $letter = FilledLetter::with('letterType')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.letters.show', compact('letter'));
    }

    /**
     * Menampilkan form untuk mengedit surat yang ditolak
     */
    public function edit($id)
    {
        $letter = FilledLetter::with(['letterType', 'letterType.dataItems'])
            ->where('user_id', Auth::id())
            ->where('status', 'rejected')
            ->findOrFail($id);

        // Pastikan filled_data adalah array
        if (!is_array($letter->filled_data)) {
            // Jika bukan array, konversi dari JSON string ke array
            if (is_string($letter->filled_data)) {
                $letter->filled_data = json_decode($letter->filled_data, true) ?? [];
            } else {
                // Jika bukan string dan bukan array, inisialisasi sebagai array kosong
                $letter->filled_data = [];
            }
        }

        return view('user.letters.edit', compact('letter'));
    }

    /**
     * Menyimpan perubahan surat yang ditolak
     */
    public function update(Request $request, $id)
    {
        $letter = FilledLetter::with('letterType.dataItems')
            ->where('user_id', Auth::id())
            ->where('status', 'rejected')
            ->findOrFail($id);

        $letterType = $letter->letterType;

        // Validasi input berdasarkan data items yang diperlukan
        $rules = [];
        foreach ($letterType->dataItems as $item) {
            $rules[$item->key] = $item->required ? 'required' : 'nullable';
        }

        $validatedData = $request->validate($rules);

        // Memastikan awal kalimat menggunakan huruf kapital
        foreach ($validatedData as $key => $value) {
            if (is_string($value) && strlen($value) > 0) {
                $validatedData[$key] = ucfirst($value);
            }
        }

        // Update data surat
        $letter->filled_data = json_encode($validatedData);
        $letter->status = 'pending'; // Ubah status menjadi pending untuk diproses ulang
        $letter->catatan_admin = null; // Hapus catatan admin sebelumnya
        $letter->save();

        // Trigger event untuk notifikasi real-time
        event(new \App\Events\NewLetterSubmitted($letter));

        return redirect()->route('user.letters.history')
            ->with('success', 'Surat berhasil diedit dan sedang menunggu persetujuan admin.');
    }

    /**
     * Download PDF surat yang sudah disetujui
     */
    public function download($id)
    {
        $letter = FilledLetter::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->orWhere('status', 'dicetak');
            })
            ->findOrFail($id);

        // Redirect ke route admin untuk generate PDF
        return redirect()->route('admin.filled-letters.pdf', $letter->id);
    }
}
