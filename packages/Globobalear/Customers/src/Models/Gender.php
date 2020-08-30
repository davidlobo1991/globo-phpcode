<?php

namespace Globobalear\Customers\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gender extends Model
{
   
    use SoftDeletes;
    
    public $translatable = ['name'];
    protected $fillable = ['name'];
    public $timestamps = false;

    /** RELATIONS */
    public function Customers()
    {
        return $this->hasMany(Customer::class);
    }
}
