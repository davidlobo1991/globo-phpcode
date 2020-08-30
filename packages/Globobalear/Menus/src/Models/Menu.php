<?php

namespace Globobalear\Menus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Globobalear\Products\Models\SeatType;
use Illuminate\Support\Collection;

class Menu extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','seat_type_id'];
    protected $with = ['dishes'];
    //public $timestamps = false;

    /** ACCESORS */
    public function getOccupiedAttribute()
    {
        $this->load('reservationMenus');

        return $this->reservationMenus->sum('quantity');
    }


    /* RELATIONS */
    public function cartes()
    {
        return $this->belongsToMany(Carte::class);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class);
    }

    public function seatTypes()
    {
        return $this->belongsToMany(SeatType::class);
    }

    public function reservationMenu()
    {
        return $this->hasMany(ReservationMenu::class);
    }

        /** METHODS */

    /**
     * Generate transport tickets for a reservation
     *
     * @param array $ticketsData
     * @param Model $reservation
     * @return Collection
     */
     public static function generateReservationMenu(array $MenuData, Model $reservation): Collection
     {
        $reservation->reservationMenu()->delete();
       
        $reservationMenu = collect();
       
         if(isset($MenuData['menu'])) {
             foreach ($MenuData['menu'] as $menu) {
                 if ((bool)$menu['quantity']) {
                     
                     $reservationMenu->push($reservation->reservationMenu()->create([
                         'reservation_id' => $reservation->id,
                         'menu_id' => $menu['menu_id'],
                         'quantity' => $menu['quantity'],
                        // 'price' => $menu['quantity'],
                         
                        
                     ]));
                 }
             }
         }
         //dd($reservationMenu);
         return collect($reservationMenu);
     }
}
