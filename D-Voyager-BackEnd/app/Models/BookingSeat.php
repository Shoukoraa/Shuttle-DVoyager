<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'seat_id'
    ];
}