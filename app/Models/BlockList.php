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
class BlockList extends Model
{
    //    use SoftDeletes;

    use HasFactory;

    const BLOCK_ID = 'id';

    const BLOCK_USER_ID = 'user_id';

    const BLOCK_BY = 'block_by';

    const BLOCK_FOR = 'block_for';

    const BLOCK_TYPE = 'type';

    const BLOCK_DATE = 'block_date';

    /**
     * @var string
     */
    public $table = 'block_lists';

    /**
     * @var string[]
     */
    protected $dates = ['deleted_at'];

    public $fillable = [
        self::BLOCK_ID,
        self::BLOCK_USER_ID,
        self::BLOCK_BY,
        self::BLOCK_FOR,
        self::BLOCK_TYPE,
        self::BLOCK_DATE,

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
        return $this->belongsTo(CandidateInformation::class, 'user_id', 'user_id');

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
