<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\RolePermission;
use App\Role;

class Permission extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'permission',
        'label',
        'level',
    ];
    
    public function role_permissions()
    {
        return $this->hasMany(RolePermission::class);
    }
    
    public function role_permission($role)
    {
        return $this->hasMany(RolePermission::class)->where('role_id', $role->id)->first();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
