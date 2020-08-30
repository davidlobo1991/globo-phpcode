<?php

namespace Globobalear\Customers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CustomersHowYouMeetUs extends Model
{
    use SoftDeletes;
    public $table = "customers_how_you_meet_us";
    protected $fillable = ['name'];
    public $timestamps = false;

    /** RELATIONS */
    public function Customers()
    {
        return $this->hasMany(Customer::class);
    }
}
