<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory;

    const FULL_NAME = 'full_name';
    const EMAIL = 'email';
    const EMAIL_VERIFIED_AT = 'email_verified_at';
    const PASSWORD = 'password';
    const STATUS = 'status';
    const REMEMBER_TOKEN = 'remember_token';

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FULL_NAME,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::PASSWORD,
        self::STATUS,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
        self::EMAIL_VERIFIED_AT,
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Many-To-Many Relationship Method for accessing the User->roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }

    /**
     * Assign Roles to users
     *
     * @param $role
     * @return array
     */
    public function assignRole($role)
    {
        return $this->roles()->syncWithoutDetaching(
            Role::whereName($role)->firstOrFail()
        );
    }

    /**
     * Assign multiple Roles to users
     *
     * @param $roles
     * @return $this
     */
    public function assignRoles($roles)
    {
        foreach ($roles as $role) {
            $this->roles()->syncWithoutDetaching(
                Role::whereName($role)->firstOrFail()
            );
        }
        return $this;
    }

    /**
     * Check user has role
     *
     * @param string $role
     * @return string
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', strtolower($role));
        }
        return !!$role->intersect($this->roles)->count();
    }
}
