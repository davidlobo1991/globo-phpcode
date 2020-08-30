<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Globobalear\Products\Models\TicketTypePirates;
use Globobalear\Products\Models\SeatTypePirates;

class ReservationTicketPirates extends ReservationTicket
{
    const TICKET_DISCOUNT_TYPE = 1;
    protected $connection = 'pirates';

    public $table = "reservations_tickets";

    public $fillable = [
        "quantity",
        "seat_types",
        "seat_type",
        "ticket_types",
        "ticket_type",
        "passes",
        "pass",
        "unit_price",
        "unit_discount",
        "reservations_id",
        'ticket_discount_types_id',
        "areas_touroperators_id",
        'reconciled_quantity',
        'prints'
    ];

    public function seatType()
    {
        return $this->belongsTo(SeatTypePirates::class, 'seat_types');
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketTypePirates::class, 'ticket_types');
    }

    public function reservation()
    {
        return $this->belongsTo(ReservationPirates::class);
    }
}
