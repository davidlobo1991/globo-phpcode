<?php

namespace Globobalear\Products\Models;

class PivotPassesPrice extends BaseModel
{
    protected $table = 'passes_prices';
    protected $with = ['ticketType'];

    public function ticketType() {

        return $this->belongsTo(TicketType::class, "ticket_type_id");
    }

}
