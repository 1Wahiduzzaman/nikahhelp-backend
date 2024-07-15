<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class block_list
 *
 * @version May 20, 2021, 12:35 pm UTC
 */
class ProfileLog extends Model
{
    //    use SoftDeletes;

    use HasFactory;

    const PROFILE_LOG_ID = 'id';

    const PROFILE_LOG_USER_ID = 'user_id';

    const PROFILE_LOG_VISITOR_ID = 'visitor_id';

    const PROFILE_LOG_TEAM_ID = 'team_id';

    const PROFILE_LOG_COUNTRY = 'country';

    const PROFILE_LOG_CITY = 'city';

    const PROFILE_LOG_DATE = 'date';

    /**
     * @var string[]
     */
    protected $dates = ['deleted_at'];

    public $fillable = [
        self::PROFILE_LOG_ID,
        self::PROFILE_LOG_USER_ID,
        self::PROFILE_LOG_VISITOR_ID,
        self::PROFILE_LOG_VISITOR_ID,
        self::PROFILE_LOG_TEAM_ID,
        self::PROFILE_LOG_COUNTRY,
        self::PROFILE_LOG_CITY,
        self::PROFILE_LOG_DATE,

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userInfo()
    {
        return $this->belongsTo(CandidateInformation::class, 'user_id', self::PROFILE_LOG_USER_ID);

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'block_for', 'id');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getBlocklistedBy()
    {
        return $this->belongsTo(User::class, 'block_by', 'id');
    }
}
