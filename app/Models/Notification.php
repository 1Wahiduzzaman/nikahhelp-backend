<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Notification
 * @package App\Models
 * @version June 2, 2021, 1:05 pm UTC
 *
 */
class Notification extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'notifications';


//    protected $dates = ['deleted_at'];

    const ID = 'id';
    const DATA = 'data';
    const TYPE = 'type';
    const TEAM_ID = 'team_id';
    const USER_ID = 'user_id';
    const CREATED_AT = 'created_at';
    const READ_AT = 'read_at';


    protected $primaryKey = 'id';

    protected $fillable = [
        self::DATA,
        self::TYPE,
        self::TEAM_ID,
        self::USER_ID,
        self::CREATED_AT,
        self::READ_AT

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function team()
    {
        return $this->hasOne(Team::class, self::ID, Self::TEAM_ID);
    }

    public function user()
    {
        return $this->hasOne(User::class,self::ID,self::USER_ID);
    }

    public function candidateUser()
    {
        return $this->belongsTo(CandidateInformation::class, 'user_id', 'user_id');

    }

    public function repUser()
    {
        return $this->belongsTo(RepresentativeInformation::class, 'user_id', 'user_id');

    }
}
