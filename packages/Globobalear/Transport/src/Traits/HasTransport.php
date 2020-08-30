<?php
/**
 * Created by PhpStorm.
 * User: mramonell
 * Date: 11/8/17
 * Time: 14:11
 */

namespace Globobalear\Transport\Traits;


use Globobalear\Transport\Models\ReservationTransport;

trait HasTransport
{
    /** ACCESSORS & MUTATORS */
    public function getTransportPriceAttribute(): float
    {
       
        return $this->ReservationTransport->map(function($item) {
           return $item->quantity * $item->price;
        })->sum();
    }

    /** RELATIONS */
    public function reservationTransports()
    {
        return $this->hasMany(ReservationTransport::class);
    }
}
