<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    // Menampilkan daftar template surat
    public function index()
    {
        $templates = TemplateSurat::latest()->paginate(10);
        return view('admin.templates.index', compact('templates'));
    }
    
    // Menampilkan form untuk membuat template surat baru
    public function create()
    {
        return view('admin.templates.create');
    }
    
    // Menyimpan template surat baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'kode_surat' => 'required|string|max:10|unique:template_surats,kode_surat',
            'konten_template' => 'required|string',
            'aktif' => 'boolean'
        ]);
        
        TemplateSurat::create($validated);
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template surat berhasil dibuat.');
    }
    
    // Menampilkan detail template surat
    public function show($id)
    {
        $template = TemplateSurat::findOrFail($id);
        return view('admin.templates.show', compact('template'));
    }
    
    // Menampilkan form untuk mengedit template surat
    public function edit($id)
    {
        $template = TemplateSurat::findOrFail($id);
        return view('admin.templates.edit', compact('template'));
    }
    
    // Menyimpan perubahan template surat
    public function update(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);
        
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'kode_surat' => 'required|string|max:10|unique:template_surats,kode_surat,' . $id,
            'konten_template' => 'required|string',
            'aktif' => 'boolean'
        ]);
        
        $template->update($validated);
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template surat berhasil diperbarui.');
    }
    
    // Menghapus template surat
    public function destroy($id)
    {
        $template = TemplateSurat::findOrFail($id);
        $template->delete();
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template surat berhasil dihapus.');
    }
}
