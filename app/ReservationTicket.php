<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Globobalear\Products\Models\TicketType;
use Globobalear\Products\Models\SeatType;

class ReservationTicket extends Model
{
    //use HasTicketType, HasSeatType;

    protected $fillable = ['ticket_type_id', 'seat_type_id', 'reservation_id', 'quantity', 'unit_price'];


    public function getPriceWODiscount()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTotalPriceAttribute()
    {
        $pricePlusUnits = $this->getPriceWODiscount();
        $totalPrice = round($pricePlusUnits - ($pricePlusUnits * $this->unit_discount / 100), 2);
        return $totalPrice;
    }

    /** RELATIONS */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }

    public function getTicketTypeTitleAttribute()
    {
        return $this->ticketType->title ;
    }

    public function getSeatTypeTitleAttribute()
    {
        return $this->seatType->title ;
    }
}
