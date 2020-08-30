<?php

namespace Globobalear\Products\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\TicketType;

trait HasPass
{
    /** ACCESSORS */
    public function getTicketPriceAttribute(): float
    {
        $price = $this->reservationTickets->map(function ($item) {
            return $item->quantity * $item->unit_price;
        })->sum();

        return $price;
    }

    /** RELATIONS */

    /**
     * A Reservation has a Pass.
     */
    public function pass(): BelongsTo
    {
        return $this->belongsTo(Pass::class);
    }


    /** METHODS */

    /**
     * Get quantity from a seat/ticket reservation
     *
     * @param SeatType $seatType
     * @param TicketType $ticketType
     * @return int
     */
    public function getSeatsQuantity(SeatType $seatType, TicketType $ticketType)
    {
        $tickets = $this->reservationTickets->where('seat_type_id', $seatType->id)
            ->where('ticket_type_id', $ticketType->id)->first();

        $quantity = $tickets ? $tickets->quantity : 0;

        return $quantity;
    }
}
