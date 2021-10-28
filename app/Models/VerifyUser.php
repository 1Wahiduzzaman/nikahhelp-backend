<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    protected $table = 'verify_users';
    const V_USER_ID='user_id';
    const V_TOKEN='token';

    /**
     * Table field name
     * @var array
     *
     */
    protected $fillable = [
        Self::V_USER_ID,
        Self::V_TOKEN
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', Self::V_USER_ID);
    }
}
