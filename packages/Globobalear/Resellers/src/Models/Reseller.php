<?php

namespace Globobalear\Resellers\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reseller extends Model
{
    use HasTranslations;
    use SoftDeletes;
    protected $fillable = ['discount', 'company', 'name','email', 'phone', 'fax', 'address', 'city', 'address', 'postal_code','internal_comments', 'passes_seller_id', 'resellers_type_id', 'agent_type_id', 'area_id', 'gender_id', 'language_id','country_id','user_id','is_enable'];
    protected $casts = ['is_enabled' => 'boolean'];
   

    /** RELATIONS */

    public function ResellerType()
    {
        return $this->belongsTo(ResellerType::class);
    }

    public function Countries()
    {
        return $this->belongsTo(Countries::class);
    }


    public function customersLanguage()
    {
        return $this->belongsTo(CustomersLanguage::class);
    }

    
}
