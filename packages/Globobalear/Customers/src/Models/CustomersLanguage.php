<?php

namespace Globobalear\Customers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomersLanguage extends Model
{
    use SoftDeletes;
    protected $table = 'languages';
    protected $fillable = ['name'];
    public $timestamps = false;

    /** RELATIONS */
    public function Customers()
    {
        return $this->hasMany(Customer::class);
    }
}
