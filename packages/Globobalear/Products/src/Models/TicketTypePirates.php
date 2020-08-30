<?php

namespace Globobalear\Products\Models;


use App\ReservationTicket;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketTypePirates extends BaseModel
{
    const ADULT = 1;

    protected $connection = 'pirates';
    public $table = 'ticket_types';

    public function piratesTicketType()
    {
        return $this->hasOne(TicketType::class, 'pirates_ticket_type_id');
    }
}
