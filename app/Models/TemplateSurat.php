<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_template',
        'konten_template',
        'html_content',
        'aktif',
        'kategori_surat'
    ];
    
    // Alias untuk html_content
    public function getHtmlContentAttribute()
    {
        return $this->konten_template;
    }
    
    public function setHtmlContentAttribute($value)
    {
        $this->attributes['konten_template'] = $value;
    }
    
    /**
     * Get all letter types using this template
     */
    public function letterTypes()
    {
        return $this->hasMany(LetterType::class, 'template_surat_id');
    }
}
