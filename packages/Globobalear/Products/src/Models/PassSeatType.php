<?php

namespace Globobalear\Products\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class PassSeatType extends Pivot
{
    use SoftDeletes;

    protected $table = 'pass_seat_type';

    protected $fillable = [
        'pass_id',
        'seat_type_id',
        'seats_available',
        'web_available'
    ];

    protected $casts = [
        'web_available' => 'boolean'
    ];

    protected $with = ['ticketTypes'];

    /** ACCESSORS */

    /**
     * Return sum of number of reservation tickets booked for a seat type on a pass
     *
     * @return int
     */
    public function getFilledSeatsAttribute(): int
    {
        $res = config('crs.reservations-tickets-model')::
        whereHas(config('crs.reservations-tickets-relation'), function ($query) {
            $query->where('pass_id', $this->pass_id);
        })
            ->whereHas('ticketType', function ($query) {
                $query->where('take_place', 1);
            })
            ->where('seat_type_id', $this->seat_type_id)->get()->sum('quantity');

        return is_null($res) ? 0 : $res;
    }

    /**
     * Return number of free seats available for a seat type on a pass
     *
     * @return int
     */
    public function getFreeSeatsAttribute(): int
    {
        return $this->seats_available - $this->getFilledSeatsAttribute();
    }

    /** RELATIONS */
    public function ticketTypes()
    {
        return $this->belongsToMany(TicketType::class, 'passes_prices', 'pass_seat_type_id', 'ticket_type_id')
            ->using(PassesPrice::class)->withPivot('id', 'price');
    }

    public function getSeatPricesAttribute()
    {
        return $this->ticketTypes;
    }

    public function scopeWebAvailable(Builder $query) : Builder
    {
        return $query->where('web_available', true);
    }
}
