<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'schedule_id',
        'customer_id',
        'sender_type',
        'message',
        'is_read'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
