<?php

namespace Globobalear\Products\Models;


use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['description'];
    protected $fillable = ['name', 'acronym', 'description'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    

    public function products()
    {
        return $this->hasMany(\Globobalear\Products\Models\Product::class);
    }
}
