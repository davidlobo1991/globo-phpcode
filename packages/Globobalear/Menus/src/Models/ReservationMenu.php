<?php

namespace Globobalear\Menus\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ReservationMenu extends Model
{
    use SoftDeletes;
    protected $fillable = ['menu_id','reservation_id', 'quantity', 'price'];
    protected $with = ['menu'];
    /** RELATIONS */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getMenuTitleAttribute()
    {
        return $this->menu->name ;
    }
}
