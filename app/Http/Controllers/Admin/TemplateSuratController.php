<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TemplateSuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    /**
     * Menampilkan daftar semua template surat.
     * Logika filter kategori sudah dihapus.
     */
    public function index()
    {
        $user = Auth::user();
        $query = TemplateSurat::query();

        if ($user->sub_role) {
            // Admin dengan sub-role hanya melihat template miliknya atau yang dibagikan publik/terbatas
            $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('share_setting', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('share_setting', 'limited')
                         ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                             $q3->where('users.id', $user->id);
                         });
                  });
            });
        } else {
            // Admin utama melihat semua template
            $query->where('owner_id', $user->id)
                  ->orWhere('share_setting', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('share_setting', 'limited')
                         ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                             $q3->where('users.id', $user->id);
                         });
                  });
        }
        
        $templates = $query->latest()->paginate(10);
        return view('admin.templates.index', compact('templates'));
    }
    
    /**
     * Menampilkan form untuk meng-upload template surat baru.
     * Tidak ada perubahan di sini, tetapi pastikan view 'create' memiliki input tipe file.
     */
    public function create()
    {
        $admins = User::where('role', 'admin')
                      ->where('id', '!=', Auth::id()) // Kecualikan admin yang sedang login
                      ->get();
        return view('admin.templates.create', compact('admins'));
    }
    
    /**
     * Menyimpan template surat baru yang di-upload.
     * Logika diubah sepenuhnya untuk menangani file upload.
     */
    public function store(Request $request)
    {
        // 1. Validasi diubah untuk file .docx
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'file_template' => 'required|file|mimes:docx|max:2048', // Memastikan file adalah .docx
            'share_setting' => 'required|in:public,limited,private',
            'shared_users'  => 'array|nullable', // Untuk 'limited' sharing
            'shared_users.*' => 'exists:users,id', // Memastikan ID user valid
        ]);
        
        // 2. Simpan file yang di-upload ke storage
        // Folder 'templates' akan dibuat di dalam 'storage/app/public/'
        $originalName = $request->file('file_template')->getClientOriginalName();
        $path = $request->file('file_template')->storeAs('templates', $originalName, 'public');
        
        // 3. Simpan path file ke database
        $template = TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'template_path' => $path,
            'aktif'         => $request->boolean('aktif'),
            'owner_id'      => Auth::id(), // Set owner_id ke user yang sedang login
            'share_setting' => $validated['share_setting'],
        ]);

        // 4. Jika share_setting adalah 'limited', simpan user yang dibagikan
        if ($validated['share_setting'] === 'limited' && !empty($validated['shared_users'])) {
            $template->sharedWithUsers()->sync($validated['shared_users']);
        }
        
        // 5. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template surat berhasil di-upload.');
    }
    
    /**
     * Menampilkan detail template surat.
     * Tidak ada perubahan, tetapi view 'show' bisa menampilkan link download ke template asli
     * menggunakan accessor '$template->public_url' dari model.
     */
    public function show($id)
    {
        $template = TemplateSurat::findOrFail($id);
        return view('admin.templates.show', compact('template'));
    }
    
    /**
     * Menampilkan form untuk mengedit template surat.
     * Tidak ada perubahan, tetapi view 'edit' harus memungkinkan upload file baru (opsional).
     */
    public function edit($id)
    {
        $template = TemplateSurat::with('sharedWithUsers')->findOrFail($id);
        $admins = User::where('role', 'admin')
                      ->where('id', '!=', Auth::id()) // Kecualikan admin yang sedang login
                      ->get();
        $sharedUserIds = $template->sharedWithUsers->pluck('id')->toArray();
        return view('admin.templates.edit', compact('template', 'admins', 'sharedUserIds'));
    }
    
    /**
     * Menyimpan perubahan pada template surat.
     * Logika dirombak total: lebih sederhana dan mengelola file lama.
     */
    public function update(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);
        
        // Validasi, file template sekarang bersifat 'nullable' (tidak wajib diisi saat update)
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'file_template' => 'nullable|file|mimes:docx|max:2048',
            'share_setting' => 'required|in:public,limited,private',
            'shared_users'  => 'array|nullable',
            'shared_users.*' => 'exists:users,id',
        ]);
        
        // Update nama template
        $template->nama_template = $validated['nama_template'];
        $template->aktif = $request->boolean('aktif');
        $template->share_setting = $validated['share_setting'];

        // Sinkronkan user yang dibagikan jika share_setting adalah 'limited'
        if ($validated['share_setting'] === 'limited') {
            $template->sharedWithUsers()->sync($validated['shared_users'] ?? []);
        } else {
            // Jika bukan 'limited', pastikan relasi sharedWithUsers dikosongkan
            $template->sharedWithUsers()->detach();
        }

        // Cek jika ada file baru yang di-upload
        if ($request->hasFile('file_template')) {
            // 1. Hapus file lama dari storage untuk menghemat ruang
            Storage::disk('public')->delete($template->template_path);
            
            // 2. Simpan file baru
            $originalName = $request->file('file_template')->getClientOriginalName();
            $newPath = $request->file('file_template')->storeAs('templates', $originalName, 'public');
            
            // 3. Update path di database
            $template->template_path = $newPath;
        }
        
        $template->save();
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template surat berhasil diperbarui.');
    }
    
    /**
     * Menghapus template surat beserta file fisiknya.
     */
    public function destroy($id)
    {
        $template = TemplateSurat::findOrFail($id);
        
        // PENTING: Hapus file fisik dari storage sebelum menghapus record DB
        Storage::disk('public')->delete($template->template_path);
        
        // Hapus record dari database
        $template->delete();
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template surat berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | Metode generatePdf() Dihapus
    |--------------------------------------------------------------------------
    |
    | Metode ini tidak lagi relevan karena:
    | 1. Sistem sekarang menggunakan template .docx, bukan HTML untuk PDF.
    | 2. Tugas controller ini adalah MENGELOLA template (CRUD), bukan MENGGUNAKANNYA
    |    untuk membuat dokumen. Proses pembuatan dokumen ada di controller lain.
    |
    */
}
