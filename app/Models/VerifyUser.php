<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    protected $table = 'verify_users';

    const V_USER_ID = 'user_id';

    const V_TOKEN = 'token';

    /**
     * Table field name
     *
     * @var array
     */
    protected $fillable = [
        self::V_USER_ID,
        self::V_TOKEN,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', self::V_USER_ID);
    }
}
