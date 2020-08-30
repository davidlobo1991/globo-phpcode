<?php

namespace Globobalear\Packs\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Globobalear\Packs\Models\Pack;

class ViewPack extends Model
{
   
    protected $table = 'viewpacks';
    protected $primaryKey = 'id';
    //protected $fillable = ['title', 'acronym','date_start','date_end'];
    protected $dates = ['date_start', 'date_end'];
   // protected $with = ['packline','packlinePirates'];


        /** METHODS */
        public function setDateStartAttribute($value)
        {
            $this->attributes['date_start'] = Carbon::parse($value)->format('Y-m-d');
        }
    
        public function setDateEndAttribute($value)
        {
            $this->attributes['date_end'] = Carbon::parse($value)->format('Y-m-d');
        }
    
        public function getDateStartAttribute($value)
        {
            return Carbon::parse($value)->format('d-m-Y');
        }
    
        public function getDateEndAttribute($value)
        {
            return Carbon::parse($value)->format('d-m-Y');
        }
    
    
        public function getDateStartReservationsAttribute()
        {
            return Carbon::parse( $this->attributes['date_start'])->format('Y-m-d');
        }
    
        public function getDateEndReservationsAttribute()
        {
            return Carbon::parse( $this->attributes['date_end'])->format('Y-m-d');
        }

}
