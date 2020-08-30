<?php

namespace Globobalear\Menus\Models;

use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\SeatType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carte extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'is_enable','product_id','seat_type_id'];
    
    //public $timestamps = false;

    /* RELATIONS */
    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }

    public function product()
    {
        return $this->belongsToMany(Product::class);
    }

    public function seatTypes()
    {
        return $this->belongsToMany(SeatType::class);
    }
}
