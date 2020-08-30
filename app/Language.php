<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'iso', 'is_enable'];

    /* SCOPES */
    public function scopeEnable($query)
    {
        $query->where('is_enable', 1);
    }
}
