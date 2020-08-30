<?php

namespace Globobalear\Products\Models;

class ProductsPrice extends BaseModel
{
    protected $table = 'products_prices';
    protected $with = ['ticketType'];

    public function seatTypes() {

        return $this->belongsTo(SeatType::class, "seat_type_id");
    }

    public function ticketType() {

        return $this->belongsTo(TicketType::class, "ticket_type_id");
    }

}
