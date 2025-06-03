<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jenis',
        'deskripsi',
        'template_surat_id',
        'kode_surat',
        'last_number',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Get the template for this letter type
     */
    public function templateSurat()
    {
        return $this->belongsTo(TemplateSurat::class, 'template_surat_id');
    }

    /**
     * Get all data items for this letter type
     */
    public function dataItems()
    {
        return $this->belongsToMany(DataItem::class, 'letter_type_data_item');
    }

    /**
     * Get all filled letters of this type
     */
    public function filledLetters()
    {
        return $this->hasMany(FilledLetter::class);
    }
}
