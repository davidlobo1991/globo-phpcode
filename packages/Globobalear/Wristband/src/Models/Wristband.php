<?php

namespace Globobalear\Wristband\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wristband extends Model
{
    use HasTranslations;
    use SoftDeletes;
    protected $fillable = ['title', 'acronym'];

    public function wristbandPasses()
    {
        return $this->hasMany(\Globobalear\Wristband\Models\WristbandPass::class);
    }

}
