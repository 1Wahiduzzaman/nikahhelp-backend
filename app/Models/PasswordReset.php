<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\PasswordBroker;

class PasswordReset extends Model
{
    const ID = 'id';
    const EMAIL = 'email';
    const TOKEN = 'token';
    const UPDATED_AT = null;
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        self::ID,
        self::EMAIL,
        self::TOKEN
    ];
}
