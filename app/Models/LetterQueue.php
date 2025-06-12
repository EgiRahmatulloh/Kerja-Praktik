<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'filled_letter_id',
        'service_schedule_id',
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

    /**
     * Get the service schedule associated with this queue
     */
    public function serviceSchedule()
    {
        return $this->belongsTo(ServiceSchedule::class);
    }
}
