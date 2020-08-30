<?php

namespace Globobalear\Products\Traits;


use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Globobalear\Products\Models\SeatType;

trait HasSeatType
{
    use HasRelationships;

    /**
     * A Reservation Ticket has a Seat Type.
     */
    public function seatType(): BelongsTo
    {
        return $this->belongsTo(SeatType::class);
    }
}
