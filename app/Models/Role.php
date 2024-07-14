<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Use slug instead of id field
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function admins()
    {
        return $this->belongsToMany('App\Models\Admin', 'admins', 'admin_id');
    }


    /**
     * Many-To-Many Relationship Method for accessing the Role->permissions
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission');
    }


    /**
     * Assign permission to certain roles
     */
    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->syncWithoutDetaching($permission);
    }
}
