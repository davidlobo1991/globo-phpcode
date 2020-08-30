<?php

namespace Globobalear\Products\Traits;


use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Globobalear\Products\Models\TicketType;

trait HasTicketType
{
    use HasRelationships;

    /**
     * A Reservation Ticket has a Ticket Type.
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }
}
