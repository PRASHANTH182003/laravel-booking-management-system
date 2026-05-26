<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_number',
        'room_type',
        'description',
        'price_per_night',
        'capacity',
        'floor',
        'status',
        'amenities',
        'image',
    ];

    protected $casts = [
        'amenities'       => 'array',
        'price_per_night' => 'decimal:2',
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('room_type', $type);
    }

    public function isAvailableForDates($checkIn, $checkOut)
    {
        return !$this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })->exists();
    }
}
