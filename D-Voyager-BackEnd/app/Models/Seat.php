<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'schedule_id',
        'seat_number',
        'status'
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_seats');
    }
}