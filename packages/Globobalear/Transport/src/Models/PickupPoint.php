<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickupPoint extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'latitude', 'longitude', 'mapaddress', 'city_id'];

    /** RELATIONS */
    public function routes()
    {
        return $this->belongsToMany(Route::class)
            ->withPivot('price', 'hour', 'order')
            ->withTimestamps()
            ->orderBy('order');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
