<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['name', 'area_id'];

    /** RELATIONS */
    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function pickupPoints()
    {
        return $this->belongsToMany(PickupPoint::class)
            ->withPivot('price', 'hour', 'order')
            ->withTimestamps()
            ->orderBy('order');
    }
}
