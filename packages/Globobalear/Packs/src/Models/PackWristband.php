<?php

namespace Globobalear\Packs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Globobalear\Wristband\Models\WristbandPass;
use DB;


class PackWristband extends Model
{

    public $fillable = ['pack_id', 'wristband_passes_id','price'];
    public $timestamps = false;


    /** RELATIONS */

    public function wristbands()
    {
        return $this->belongsTo(WristbandPass::class,'wristband_passes_id');
    }

    public function getFilledSeatsAttribute(): int
    {

        $query = DB::select( DB::raw("select sum(reservation_pack_wristbands.quantity) AS quantity
        from reservations inner join reservation_pack_wristbands on reservations.id = reservation_pack_wristbands.reservation_id
        where reservations.finished = 1 AND reservations.canceled_date IS NULL and reservations.deleted_at IS NULL
        GROUP by reservation_pack_wristbands.quantity"));

        /*$query = DB::select( DB::raw("select sum(reservation_pack_wristbands.quantity) AS quantity
        from reservations inner join reservation_pack_wristbands on reservations.id = reservation_pack_wristbands.reservation_id
        where reservations.pack_id = ". $this->pack_id ." GROUP by reservation_pack_wristbands.quantity"));*/

        $query = collect($query);

        return is_null($query->first()) ? 0 : $query->first()->quantity;
    }

}
