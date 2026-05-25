<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'schedule_id',
        'booking_time',
        'status',
        'total_seat',
        'passenger_name',
        'passenger_phone',
        'passenger_email',
        'price_per_seat',
        'total_price',
        'service_fee'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class)->withTrashed();
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seats');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}