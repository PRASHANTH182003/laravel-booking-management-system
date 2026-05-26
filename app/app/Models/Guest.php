<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'full_name',
        'id_type',
        'id_number',
        'nationality',
        'date_of_birth',
        'phone',
        'email',
        'is_primary',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_primary'    => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
