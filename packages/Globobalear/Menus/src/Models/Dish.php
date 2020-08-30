<?php

namespace Globobalear\Menus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dish extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description_allergens', 'vegetarian', 'dishes_type_id'];

    //public $timestamps = false;

    /** RELATIONS **/
    public function menus()
    {
        return $this->belongsToMany(Menus::class);
    }

    public function dishesType()
    {
        return $this->belongsToMany(DishesType::class);
    }
}
