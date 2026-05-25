<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'vehicle_category',
        'capacity',
        'driver_id',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}