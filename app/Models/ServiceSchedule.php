<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'is_active',
        'processing_time',
        'is_paused',
        'pause_message',
        'pause_end_time'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_paused' => 'boolean',
        'pause_end_time' => 'datetime',
    ];
}
