<?php
/**
 * Created by PhpStorm.
 * User: amxa
 * Date: 17/12/17
 * Time: 2:36
 */

namespace Globobalear\Products\Models;


class PivotPassSeatType extends BaseModel
{
    public $table = "pass_seat_type";
    protected $with = ['passes_prices'];

    public function passesPrice()
    {
        $this->belongsTo(PassesPrice::class, 'pass_seat_type_id');
    }

    public function passes_prices() {
        return $this->hasMany(PivotPassesPrice::class, 'pass_seat_type_id');
    }

    public function seat_types() {

        return ($this->belongsTo(SeatType::class, "seat_type_id"));
    }

    /**
     * Undocumented function
     *
     * @param [type] $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->whereHas('seat_types', function($q){
            $q->where('is_enable', 1);
        });
    }

//    public function shows()
//    {
//        return $this->belongsTo(\App\Models\Shows::class, "seat_types_id");
//    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class, "seat_type_id");
    }

}
