<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataItem;
use App\Models\LetterType;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class LetterTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Menampilkan daftar jenis surat
    public function index()
    {
        $letterTypes = LetterType::with('templateSurat')->latest()->paginate(10);
        return view('admin.letter-types.index', compact('letterTypes'));
    }

    // Menampilkan form untuk membuat jenis surat baru
    public function create()
    {
        $templates = TemplateSurat::where('aktif', true)->get();
        $dataItems = DataItem::all();
        return view('admin.letter-types.create', compact('templates', 'dataItems'));
    }

    // Menyimpan jenis surat baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kode_surat' => 'required|string|max:10|unique:letter_types,kode_surat',
            'template_surat_id' => 'required|exists:template_surats,id',
            'is_public' => 'boolean',
            'data_items' => 'required|array',
            'data_items.*' => 'exists:data_items,id'
        ]);

        $letterType = LetterType::create([
            'nama_jenis' => $validated['nama_jenis'],
            'deskripsi' => $validated['deskripsi'],
            'kode_surat' => $validated['kode_surat'],
            'template_surat_id' => $validated['template_surat_id'],
            'is_public' => $request->has('is_public') ? $request->is_public : false,
            'last_number' => 0
        ]);

        $letterType->dataItems()->attach($validated['data_items']);

        return redirect()->route('admin.letter-types.index')
            ->with('success', 'Jenis surat berhasil dibuat.');
    }

    // Menampilkan detail jenis surat
    public function show($id)
    {
        $letterType = LetterType::with(['templateSurat', 'dataItems'])->findOrFail($id);
        return view('admin.letter-types.show', compact('letterType'));
    }

    // Menampilkan form untuk mengedit jenis surat
    public function edit($id)
    {
        $letterType = LetterType::with('dataItems')->findOrFail($id);
        $templates = TemplateSurat::where('aktif', true)->get();
        $dataItems = DataItem::all();
        $selectedDataItems = $letterType->dataItems->pluck('id')->toArray();

        return view('admin.letter-types.edit', compact('letterType', 'templates', 'dataItems', 'selectedDataItems'));
    }

    // Menyimpan perubahan jenis surat
    public function update(Request $request, $id)
    {
        $letterType = LetterType::findOrFail($id);

        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kode_surat' => 'required|string|max:10|unique:letter_types,kode_surat,' . $id,
            'template_surat_id' => 'required|exists:template_surats,id',
            'is_public' => 'boolean',
            'data_items' => 'required|array',
            'data_items.*' => 'exists:data_items,id'
        ]);

        $letterType->update([
            'nama_jenis' => $validated['nama_jenis'],
            'deskripsi' => $validated['deskripsi'],
            'kode_surat' => $validated['kode_surat'],
            'template_surat_id' => $validated['template_surat_id'],
            'is_public' => $request->has('is_public') ? $request->is_public : false
        ]);

        $letterType->dataItems()->sync($validated['data_items']);

        return redirect()->route('admin.letter-types.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    // Menghapus jenis surat
    public function destroy($id)
    {
        $letterType = LetterType::findOrFail($id);
        $letterType->delete();

        return redirect()->route('admin.letter-types.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
