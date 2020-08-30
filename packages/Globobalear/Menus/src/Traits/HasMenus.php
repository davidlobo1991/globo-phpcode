<?php
/**
 * Created by PhpStorm.
 * User: mramonell
 * Date: 11/8/17
 * Time: 14:11
 */

namespace Globobalear\Menus\Traits;


use Globobalear\Menus\Models\ReservationMenu;

trait HasMenu
{
    /** ACCESSORS & MUTATORS */
    public function getMenuPriceAttribute(): float
    {
        return $this->reservationMenus->map(function($item) {
           //return $item->quantity * $item->price;
           return $item->quantity;
        })->sum();
    }

    /** RELATIONS */
    public function reservationMenus()
    {
        return $this->hasMany(ReservationMenus::class);
    }
}
