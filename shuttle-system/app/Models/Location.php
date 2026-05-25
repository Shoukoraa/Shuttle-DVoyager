<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    public function originRoutes()
    {
        return $this->hasMany(Route::class, 'origin_location_id');
    }

    public function destinationRoutes()
    {
        return $this->hasMany(Route::class, 'destination_location_id');
    }
}
