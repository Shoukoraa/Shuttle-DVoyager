<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use SoftDeletes;
    // Tidak ada timestamps() di migration
    public $timestamps = false;

    protected $fillable = [
        'origin_location_id',
        'destination_location_id',
        'distance_km',
        'price'
    ];

    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_location_id')->withTrashed();
    }

    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_location_id')->withTrashed();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
