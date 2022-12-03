<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ShortListedCandidate
 * @package App\Models
 * @version April 29, 2021, 7:36 am UTC
 *
 */
class ShortListedRepresentative extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'short_listed_representative';

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];


    public $fillable = [
        "id",
        "user_id",
        "shortlisted_by",
        "shortlisted_for",
        "shortlisted_date"
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
        "user_id" => 'required',
        "shortlisted_by" => 'required'
    ];

    public function userInfo()
    {
        return $this->belongsTo(RepresentativeInformation::class, 'user_id', 'user_id');

    }

    public function getTeam()
    {
        return $this->belongsTo(Team::class, 'shortlisted_for', 'id');

    }

    public function getShortlistedBy()
    {
        return $this->belongsTo(User::class, 'shortlisted_by', 'id');
    }

}
