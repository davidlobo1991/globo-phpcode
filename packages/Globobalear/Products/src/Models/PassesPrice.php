<?php

namespace Globobalear\Products\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PassesPrice extends Pivot
{
    use SoftDeletes;
    protected $table = 'passes_prices';
    protected $fillable = ['pass_seat_type_id', 'ticket_type_id', 'price'];
}
