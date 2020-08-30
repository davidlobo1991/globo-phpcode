<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Globobalear\Products\Models\ShowPirates;
use Globobalear\Products\Models\PassPirates;
use Globobalear\Products\Models\SeatTypePirates;
use Globobalear\Products\Models\TicketTypePirates;
use App\ReservationPack;

class ReservationPackPirates extends ReservationPack
{
    protected $connection = 'pirates';
    protected $table = 'reservation_packs';

    protected $fillable = [
        "pack_id",
        "show_id",
        "seat_type_id",
        "ticket_type_id",
        "unit_price",
        'reservation_id',
        'pass_id',
        'quantity',
    ];

    public function getTitleShowAttribute()
    {
        return $this->shows->title;
    }

    public function shows()
    {
        return $this->belongsTo(ShowPirates::class, 'show_id');
    }

    public function passes()
    {
        return $this->belongsTo(PassPirates::class, 'pass_id', 'id');
    }


    public function ticketType()
    {
        return $this->belongsTo(TicketTypePirates::class, 'ticket_type_id');
    }

    public function seatTypes()
    {
        return $this->belongsTo(SeatTypePirates::class, 'seat_type_id');
    }
}
