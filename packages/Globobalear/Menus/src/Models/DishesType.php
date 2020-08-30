<?php

namespace Globobalear\Menus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DishesType extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];
    public $timestamps = false;
}
