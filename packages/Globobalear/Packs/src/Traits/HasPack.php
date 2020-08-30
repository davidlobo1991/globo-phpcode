<?php

namespace Globobalear\Packs\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;
use Globobalear\Packs\Models\Pack;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\TicketType;

trait HasPack
{
    /** ACCESSORS */
    public function getTicketPackPriceAttribute(): float
    {
        $product = $this->reservationPacks->map(function ($item) {
            return $item->quantity * $item->unit_price;
        })->sum();

        $shows = $this->reservationPacksPirates->map(function ($item) {
            return $item->quantity * $item->unit_price;
        })->sum();

        if($this->reservationPacksWristband){
           $wirstband = $this->reservationPacksWristband->quantity * $this->reservationPacksWristband->unit_price;
        }
        else {
            $wirstband = 0;
        }
       
     
        $total = $product + $shows + $wirstband; 

        return $total;
    }

    /** RELATIONS */

    /**
     * A Reservation has a Pass.
     */
    public function pass(): BelongsTo
    {
        return $this->belongsTo(Pass::class);
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


   
}
