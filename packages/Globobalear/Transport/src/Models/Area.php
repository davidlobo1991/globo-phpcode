<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'acronym'];
}
