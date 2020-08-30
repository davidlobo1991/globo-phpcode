@if($seatType->pivot->freeSeats == 0 && $product->has_passes)
    red
@elseif($seatType->pivot->freeSeats < $global->amber_trigger && $product->has_passes)
    amber
@else
    green
@endif
