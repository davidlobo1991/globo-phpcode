<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PassesSeller extends Model
{
    const DIRECT = 1;
    protected $fillable = ['name'];
    use SoftDeletes;

    /** RELATIONS */
    public function channels()
    {
        return $this->hasMany(Channel::class)->where('is_enable', 1);
    }
}
