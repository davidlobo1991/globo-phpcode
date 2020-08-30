<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transporter extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    /** RELATIONS */
    public function buses()
    {
        return $this->hasMany(Bus::class);
    }
}
