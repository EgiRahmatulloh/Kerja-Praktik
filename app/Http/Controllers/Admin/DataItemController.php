<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataItem;
use Illuminate\Http\Request;

class DataItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Menampilkan daftar variabel surat
    public function index()
    {
        $dataItems = DataItem::latest()->paginate(10);
        return view('admin.data-items.index', compact('dataItems'));
    }

    // Menampilkan form untuk membuat variabel surat baru
    public function create()
    {
        return view('admin.data-items.create');
    }

    // Menyimpan variabel surat baru
    public function store(Request $request)
    {
        // Set default value for checkbox and ensure capitalization
        $request->merge([
            'required' => $request->has('required') ? true : false,
            'label' => ucfirst($request->label), // Memastikan huruf pertama kapital
            'help_text' => $request->has('help_text') ? ucfirst($request->help_text) : null // Memastikan huruf pertama kapital jika ada
        ]);

        $validated = $request->validate([
            'key' => 'required|string|max:100|unique:data_items',
            'label' => 'required|string|max:255',
            'tipe_input' => 'required|string|max:50',
            'opsi' => 'nullable|json',
            'required' => 'boolean',
            'help_text' => 'nullable|string|max:255'
        ]);

        DataItem::create($validated);

        return redirect()->route('admin.data-items.index')
            ->with('success', 'Variabel surat berhasil dibuat.');
    }

    // Menampilkan detail variabel surat
    public function show($id)
    {
        $dataItem = DataItem::findOrFail($id);
        return view('admin.data-items.show', compact('dataItem'));
    }

    // Menampilkan form untuk mengedit variabel surat
    public function edit($id)
    {
        $dataItem = DataItem::findOrFail($id);
        return view('admin.data-items.edit', compact('dataItem'));
    }

    // Menyimpan perubahan variabel surat
    public function update(Request $request, $id)
    {
        $dataItem = DataItem::findOrFail($id);

        // Set default value for checkbox and ensure capitalization
        $request->merge([
            'required' => $request->has('required') ? true : false,
            'label' => ucfirst($request->label), // Memastikan huruf pertama kapital
            'help_text' => $request->has('help_text') ? ucfirst($request->help_text) : null // Memastikan huruf pertama kapital jika ada
        ]);

        $validated = $request->validate([
            'key' => 'required|string|max:100|unique:data_items,key,' . $id,
            'label' => 'required|string|max:255',
            'tipe_input' => 'required|string|max:50',
            'opsi' => 'nullable|json',
            'required' => 'boolean',
            'help_text' => 'nullable|string|max:255'
        ]);

        $dataItem->update($validated);

        return redirect()->route('admin.data-items.index')
            ->with('success', 'Variabel surat berhasil diperbarui.');
    }

    // Menghapus variabel surat
    public function destroy($id)
    {
        $dataItem = DataItem::findOrFail($id);
        $dataItem->delete();

        return redirect()->route('admin.data-items.index')
            ->with('success', 'Variabel surat berhasil dihapus.');
    }
}
