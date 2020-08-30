<?php

namespace Globobalear\Payments\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Globobalear\Payments\Models\PaymentMethod;

trait HasPayments
{
    /** ACCESSORS & MUTATORS */
    public function getPendingToPayAttribute(): float
    {
        $total = $this->totalPrice;

        $paid = $this->paymentMethods->map(function ($item) {
            return $item->pivot->total;
        })->sum();

        return $total - $paid;
    }

    public function getPendingPackToPayAttribute(): float
    {
       
        $total = $this->totalPackPrice;

        $paid = $this->paymentMethods->map(function ($item) {
            return $item->pivot->total;
        })->sum();

        return $total - $paid;
    }


    /** RELATIONS */
    

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::Class, 'payment_method_reservations')
            ->withPivot('total', 'user_id','id')->withTimestamps()->whereNull('payment_method_reservations.deleted_at');
    }
}
