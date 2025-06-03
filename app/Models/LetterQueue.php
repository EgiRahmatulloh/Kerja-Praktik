<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'filled_letter_id',
        'scheduled_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
    ];

    /**
     * Get the filled letter associated with this queue
     */
    public function filledLetter()
    {
        return $this->belongsTo(FilledLetter::class);
    }
}
