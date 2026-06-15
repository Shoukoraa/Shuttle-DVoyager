<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleLocation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'schedule_id',
        'latitude',
        'longitude',
        'recorded_at'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}