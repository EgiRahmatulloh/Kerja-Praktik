<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TemplateSuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    // Menampilkan daftar template surat
    public function index(Request $request)
    {
        $query = TemplateSurat::query();
        
        // Filter berdasarkan kategori jika ada parameter
        if ($request->has('kategori')) {
            $query->where('kategori_surat', $request->kategori);
        } else {
            // Default menampilkan kategori 'default'
            $query->where('kategori_surat', 'default');
        }
        
        $templates = $query->latest()->paginate(10);
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
            'konten_template' => 'required|string',
            'aktif' => 'boolean',
            'kategori_surat' => 'required|in:default,form,non_form'
        ]);
        
        // Set kategori default sebagai 'default'
        $validated['kategori_surat'] = 'default';
        
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
            'konten_template' => 'required|string',
            'aktif' => 'boolean',
            'kategori_surat' => 'required|in:default,form,non_form',
            'action_type' => 'required|in:update_original,save_as_new'
        ]);
        
        if ($request->action_type === 'save_as_new') {
            // Buat template baru dengan kategori yang dipilih
            $newTemplate = $template->replicate();
            $newTemplate->kategori_surat = $validated['kategori_surat'];
            $newTemplate->nama_template = $validated['nama_template'];
            $newTemplate->konten_template = $validated['konten_template'];
            $newTemplate->save();
            
            $kategoriText = $validated['kategori_surat'] === 'form' ? 'Form' : ($validated['kategori_surat'] === 'non_form' ? 'Non-Form' : 'Default');
            $message = 'Template surat baru berhasil disimpan dengan kategori ' . $kategoriText . '.';
            $kategori = $validated['kategori_surat'];
        } else {
            // Update template asli
            $template->update($validated);
            $message = 'Template surat berhasil diperbarui.';
            $kategori = $template->kategori_surat;
        }
        
        // Redirect berdasarkan kategori
        if ($kategori === 'form') {
            $route = 'admin.surat-form.index';
        } elseif ($kategori === 'non_form') {
            $route = 'admin.surat-non-form.index';
        } else {
            $route = 'admin.templates.index';
        }
        
        return redirect()->route($route)
            ->with('success', $message);
    }
    
    // Menghapus template surat
    public function destroy($id)
    {
        $template = TemplateSurat::findOrFail($id);
        $kategori = $template->kategori_surat;
        $template->delete();
        
        // Redirect berdasarkan kategori
        if ($kategori === 'form') {
            $route = 'admin.surat-form.index';
        } elseif ($kategori === 'non_form') {
            $route = 'admin.surat-non-form.index';
        } else {
            $route = 'admin.templates.index';
        }
        
        return redirect()->route($route)
            ->with('success', 'Template surat berhasil dihapus.');
    }

    // Menghasilkan file PDF dari template surat
    public function generatePdf($id)
    {
        $template = TemplateSurat::findOrFail($id);
        
        // Pastikan hanya template non-form yang bisa di-generate PDF
        if ($template->kategori_surat !== 'non_form') {
            return redirect()->back()->with('error', 'Hanya template surat non-form yang dapat di-generate PDF.');
        }
        
        // Konten template untuk PDF
        $content = $template->konten_template;
        
        // Proses placeholder bulan dan tahun
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        // Ganti placeholder bulan
        $content = str_replace("{{ \$data->bulan }}", $currentMonth, $content);
        $content = str_replace("{{{ \$data->bulan }}}", $currentMonth, $content);
        $content = str_replace("{{\$data->bulan}}", $currentMonth, $content);
        $content = str_replace("{{ \$bulan }}", $currentMonth, $content);
        $content = str_replace("{{{ \$bulan }}}", $currentMonth, $content);
        $content = str_replace("{{\$bulan}}", $currentMonth, $content);
        
        // Ganti placeholder tahun
        $content = str_replace("{{ \$data->tahun }}", $currentYear, $content);
        $content = str_replace("{{{ \$data->tahun }}}", $currentYear, $content);
        $content = str_replace("{{\$data->tahun}}", $currentYear, $content);
        $content = str_replace("{{ \$tahun }}", $currentYear, $content);
        $content = str_replace("{{{ \$tahun }}}", $currentYear, $content);
        $content = str_replace("{{\$tahun}}", $currentYear, $content);
        
        // Ganti format gabungan /bulan/tahun
        $content = str_replace("/{{ \$data->bulan }}/{{ \$data->tahun }}", "/$currentMonth/$currentYear", $content);
        $content = str_replace("/{{{ \$data->bulan }}}/{{{ \$data->tahun }}}", "/$currentMonth/$currentYear", $content);
        $content = str_replace("/{{\$data->bulan}}/{{\$data->tahun}}", "/$currentMonth/$currentYear", $content);
        $content = str_replace("/{{ \$bulan }}/{{ \$tahun }}", "/$currentMonth/$currentYear", $content);
        $content = str_replace("/{{{ \$bulan }}}/{{{ \$tahun }}}", "/$currentMonth/$currentYear", $content);
        $content = str_replace("/{{\$bulan}}/{{\$tahun}}", "/$currentMonth/$currentYear", $content);
        
        // Generate PDF
        $pdf = Pdf::loadView('print.template_pdf', [
            'template' => $template,
            'content' => $content
        ]);
        
        return $pdf->stream('template_' . $template->nama_template . '.pdf');
    }
}
