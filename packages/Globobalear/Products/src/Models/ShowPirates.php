<?php

namespace Globobalear\Products\Models;

class ShowPirates extends BaseModel
{
    protected $connection = 'pirates';
    public $table = "shows";

    public function ticketTypes()
    {
        return $this->belongsToMany(TicketTypePirates::class, 'shows_ticket_types', 'show_id', 'ticket_type_id');
    }

    public function seatTypes()
    {
        return $this->belongsToMany(SeatTypePirates::class, 'shows_seat_types', 'show_id', 'seat_type_id');
    }
}
