<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilledLetter extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'letter_type_id',
        'filled_data',
        'status',
        'no_surat',
        'catatan_admin'
    ];
    
    protected $casts = [
        'filled_data' => 'array'
    ];
    
    /**
     * Get the user who filled this letter
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the letter type of this filled letter
     */
    public function letterType()
    {
        return $this->belongsTo(LetterType::class);
    }
    
    /**
     * Get the filled_data attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getFilledDataAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value;
    }
    
    /**
     * Set the filled_data attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setFilledDataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['filled_data'] = json_encode($value);
        } else {
            $this->attributes['filled_data'] = $value;
        }
    }

    /**
     * Get the queue record for this letter
     */
    public function queue()
    {
        return $this->hasOne(LetterQueue::class);
    }
}
