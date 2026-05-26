<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'guests',
        'total_amount',
        'status',
        'special_requests',
        'payment_status',
        'payment_method',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
        'confirmed_at'   => 'datetime',
        'cancelled_at'   => 'datetime',
        'total_amount'   => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            $booking->booking_reference = 'BK-' . strtoupper(Str::random(8));
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    // Computed attributes
    public function getNightsAttribute()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending'     => '<span class="badge bg-warning">Pending</span>',
            'confirmed'   => '<span class="badge bg-success">Confirmed</span>',
            'checked_in'  => '<span class="badge bg-primary">Checked In</span>',
            'checked_out' => '<span class="badge bg-secondary">Checked Out</span>',
            'cancelled'   => '<span class="badge bg-danger">Cancelled</span>',
            default       => '<span class="badge bg-light">Unknown</span>',
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }
}
