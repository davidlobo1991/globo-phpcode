<?php

namespace Globobalear\Resellers\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResellerPirates extends Model
{
    public $table = "resellers";

    protected $connection = 'pirates';
    
    public $fillable = [
        "clientid",
        "company",
        "types_id",
        "agent_types_id",
        "areas_id",
        "billing_agents_id",
        "nif_cif",
        "languages_id",
        "manager",
        "contact_firstname",
        "contact_surname",
        "address",
        "city",
        "postal_code",
        "countries_id",
        "phone",
        "fax",
        "resellers_types_id",
        "email",
        "discount"
    ];
}
