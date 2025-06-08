<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Penting untuk helper path

class TemplateSurat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Kolom ini disesuaikan dengan skema database yang baru.
     * - 'konten_template' dan 'kategori_surat' dihapus.
     * - 'template_path' ditambahkan.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_template',
        'template_path', // Menggantikan 'konten_template'
        'aktif',
    ];

    /**
     * The accessors to append to the model's array form.
     * 
     * Menambahkan atribut virtual 'full_path' agar mudah diakses.
     *
     * @var array
     */
    protected $appends = ['full_path', 'public_url'];

    // -------------------------------------------------------------------------
    // ALIAS DAN ATTRIBUTE LAMA DIHAPUS
    // -------------------------------------------------------------------------
    // Fungsi getHtmlContentAttribute() dan setHtmlContentAttribute()
    // sudah tidak relevan lagi karena kita tidak lagi menyimpan HTML.
    // Menghapusnya akan membuat model lebih bersih dan tidak membingungkan.
    // -------------------------------------------------------------------------


    // =========================================================================
    // ACCESSORS (Atribut Virtual untuk Mempermudah)
    // =========================================================================

    /**
     * Accessor untuk mendapatkan path absolut ke file template di storage.
     * Sangat berguna di controller saat memproses dengan PHPWord.
     *
     * Cara penggunaan: $template->full_path
     *
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        // Mengembalikan path absolut, contoh:
        // /var/www/project/storage/app/public/templates/namafile.docx
        return Storage::disk('public')->path($this->template_path);
    }

    /**
     * Accessor untuk mendapatkan URL publik ke file template.
     * Berguna jika Anda ingin membuat link download langsung ke template aslinya.
     *
     * Cara penggunaan: $template->public_url
     *
     * @return string
     */
    public function getPublicUrlAttribute(): string
    {
        // Mengembalikan URL, contoh:
        // http://localhost:8000/storage/templates/namafile.docx
        return Storage::disk('public')->url($this->template_path);
    }

    // =========================================================================
    // RELATIONS (Relasi)
    // =========================================================================
    
    /**
     * Relasi ini tetap valid dan tidak perlu diubah.
     * Mendapatkan semua Jenis Surat yang menggunakan template ini.
     */
    public function letterTypes()
    {
        return $this->hasMany(LetterType::class, 'template_surat_id');
    }
}