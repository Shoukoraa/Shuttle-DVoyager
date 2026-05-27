<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'gateway',
        'gateway_transaction_id',
        'payment_url',
        'gateway_response',
        'status',
        'payment_time',
    ];

    protected $casts = [
        'payment_time' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
