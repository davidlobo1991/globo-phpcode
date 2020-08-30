<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class City extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    /** RELATIONS */
    public function pickupPoints()
    {
        return $this->hasMany(PickupPoint::class);
    }
}
