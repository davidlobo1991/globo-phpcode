<?php

namespace Globobalear\Resellers\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResellersType extends Model
{
    
    use SoftDeletes;
    
    public $translatable = ['name'];
    protected $fillable = ['name'];
    //public $timestamps = false;

   
     /** RELATIONS */
     public function Resellers()
     {
         return $this->hasMany(Reseller::class);
     }
}
