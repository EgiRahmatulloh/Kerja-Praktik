<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'key',
        'label',
        'tipe_input',
        'opsi',
        'required',
        'help_text'
    ];
    
    protected $casts = [
        'opsi' => 'array'
    ];
    
    /**
     * Get all letter types that use this data item
     */
    public function letterTypes()
    {
        return $this->belongsToMany(LetterType::class, 'letter_type_data_item');
    }
}
