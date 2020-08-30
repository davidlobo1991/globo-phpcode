<?php

namespace Globobalear\Promocodes\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Globobalear\Promocodes\Models\Promocode;

trait HasPromocodes
{
    /** RELATIONS */

    /**
     * A Reservation has a Promocode.
     */
     public function getPromocodeAttribute($total): float
     {
         return $total * $this->discount /100;
     }

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class);
    }
}
