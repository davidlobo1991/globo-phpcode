<?php

namespace Globobalear\Wristband\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Globobalear\Wristband\Models\WristbandPass;

trait HasPaymentWristbandPass
{
    /**
     * @return float
     */
    public function getPendingToPayWristbandPassAttribute(): float
    {
        $paid = $this->reservationWristbandPasses->map(function ($wristbandPass) {
            return $wristbandPass->pivot->quantity * $wristbandPass->price;
        })->sum();

        return $paid; //$total - $paid;
    }

    /**
     * @return float
     */
    public function getTotalPriceTicketWristbandPassAttribute(): float
    {
        $total = $this->reservationWristbandPasses->map(function ($wristbandPass) {
            return $wristbandPass->pivot->quantity * $wristbandPass->price;
        })->sum();

        return $total ?? 0;
    }

    

}
