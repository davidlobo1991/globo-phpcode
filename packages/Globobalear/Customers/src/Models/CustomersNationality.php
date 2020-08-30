<?php

namespace Globobalear\Customers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomersNationality extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name'];
    public $timestamps = false;

    /** RELATIONS */
    public function Customers()
    {
        return $this->hasMany(Customer::class);
    }
}
