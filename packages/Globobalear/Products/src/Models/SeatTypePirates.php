<?php

namespace Globobalear\Products\Models;

class SeatTypePirates extends BaseModel
{
    const GENERIC = 7;

    protected $connection = 'pirates';
    public $table = "seat_types";
}
