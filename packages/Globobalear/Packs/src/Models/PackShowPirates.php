<?php

namespace Globobalear\Packs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Globobalear\Products\Models\SeatTypePirates;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\ShowPirates;
use Globobalear\Products\Models\PassPirates;
use Globobalear\Products\Models\TicketType;
use Globobalear\Products\Models\TicketTypePirates;

class PackShowPirates extends Model
{

    protected $connection = 'pirates';
    public $table = "pack_shows";

    protected $fillable = ['pack_id', 'show_id','seat_type_id','ticket_type_id','price'];
    public $timestamps = false;


    public function getTitleShowAttribute()
    {
        return $this->shows->title;
    }

    public function getTitleSeaTypeAttribute()
    {
        return $this->seatTypes->title;
    }

    public function getTitleTicketTypeAttribute()
    {
        return $this->ticketType->title;
    }


    /** RELATIONS */


    public function show()
    {
        return $this->belongsTo(ShowPirates::class,'show_id');
    }

    public function passes()
    {
        return $this->hasMany(PassPirates::class,'show_id','show_id');
    }

    public function seatType()
    {
        return $this->belongsTo(SeatTypePirates::class,'seat_type_id');
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketTypePirates::class,'ticket_type_id');
    }

}
