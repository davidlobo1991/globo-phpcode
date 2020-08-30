<?php

namespace Globobalear\Products\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PassSeatTypesPirates extends Pivot
{
    protected $connection = 'pirates';
    public $table = "passes_seat_types";

    

}

