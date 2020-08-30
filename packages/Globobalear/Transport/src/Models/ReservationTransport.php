<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ReservationTransport extends Model
{
    use SoftDeletes;
    protected $fillable = ['reservation_id','bus_id', 'pickup_point_id', 'quantity', 'price', 'pickup_point', 'hour'];

    /** RELATIONS */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function getRouteTitleAttribute()
    {
        return $this->bus->route->name ;
    }
}
