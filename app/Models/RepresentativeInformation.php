<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepresentativeInformation
 * @package App\Models
 * @version May 3, 2021, 10:52 am UTC
 *
 */
class RepresentativeInformation extends Model
{
    use SoftDeletes;

    use HasFactory;


    public $table = 'representative_informations';

    /**
     * @var string[]
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string[]
     */
    public const BASIC_INFO = [
        'user_id',
        'first_name',
        'last_name',
        'screen_name',
    ];

    public const ESSENTIAL_INFO = [
        'per_gender',
        'dob',
        'per_occupation',
    ];

    public const PERSONAL_INFO = [
        'per_email',
        'per_current_residence_country',
        'per_current_residence_city',
        'per_permanent_country',
        'per_permanent_city',
        'per_county',
        'per_telephone_no',
        'mobile_number',
        'mobile_country_code',
        'per_permanent_post_code',
        'per_permanent_address',
    ];

    public const VERIFICATION_INFO = [
        'is_document_upload',
        'ver_country',
        'ver_city',
        'ver_document_type',
        'ver_document_frontside',
        'ver_document_backside',
        'ver_recommender_title',
        'ver_recommender_first_name',
        'ver_recommender_last_name',
        'ver_recommender_occupation',
        'ver_recommender_address',
        'ver_recommender_mobile_no',
    ];

    public const IMAGE_UPLOAD_INFO = [
        'per_avatar_url',
        'per_main_image_url',
        'anybody_can_see',
        'only_team_can_see',
        'team_connection_can_see',
        'is_agree',
    ];


    public $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'screen_name',
        'per_gender',
        'dob',
        'per_occupation',
        'per_email',
        'per_current_residence_country',
        'per_current_residence_city',
        'per_permanent_country',
        'per_permanent_city',
        'per_county',
        'per_telephone_no',
        'mobile_number',
        'mobile_country_code',
        'per_permanent_post_code',
        'per_permanent_address',
        'is_document_upload',
        'ver_country',
        'ver_city',
        'ver_document_type',
        'ver_document_frontside',
        'ver_document_backside',
        'ver_recommender_title',
        'ver_recommender_first_name',
        'ver_recommender_last_name',
        'ver_recommender_occupation',
        'ver_recommender_address',
        'ver_recommender_mobile_no',
        'ver_recommender_email',
        'per_avatar_url',
        'per_main_image_url',
        'anybody_can_see',
        'only_team_can_see',
        'team_connection_can_see',
        'is_agree',
        'data_input_status',
        'address_1',
        'address_2',
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

    /**
     * @return mixed
     */
    public function currentResidenceCountry()
    {
        return $this->belongsTo(Country::class, 'per_current_residence_country');
    }

    /**
     * @return mixed
     */
    public function permanentCountry()
    {
        return $this->belongsTo(Country::class, 'per_permanent_country');
    }

    /**
     * @return mixed
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'per_county');
    }

    /**
     * @return mixed
     */
    public function activeTeams()
    {
        return $this->belongsToMany(Team::class,'team_members','user_id','team_id','user_id','id')->wherePivot('status',1);
    }

    /**
     * @return mixed
     */
    public function getActiveTeamAttribute()
    {
        return $this->activeTeams->first();
    }

    public function getCandidateTeamAttribute() {
        return $this->belongsToMany(Team::class, 'team_members', 'user_id', 'team_id', 'user_id', 'id')->wherePivot('user_type', 'Candidate')->first();
    }
    
    /**
     * Return Candidate information short listed by user
     * @return BelongsToMany
     */
    public function shortList()
    {
        return $this->belongsToMany(CandidateInformation::class, 'short_listed_candidates', 'shortlisted_by', 'user_id','user_id','user_id')->withTimestamps();
    }

    /**
     * Return Candidate information team listed by user
     * @return BelongsToMany
     */
    public function teamList()
    {
        return $this->belongsToMany(CandidateInformation::class, 'team_listed_candidates', 'team_listed_by', 'user_id','user_id','user_id')->withTimestamps();
    }
    
    /**
     * Return Representative block listed by user
     * @return BelongsToMany
     */
    public function blockList()
    {
        return $this->belongsToMany(CandidateInformation::class, 'block_lists', 'block_by', 'user_id','user_id','user_id')->withTimestamps();

    }

   

    

    

    public function ticketSubmission()
    {
        return $this->hasMany(TicketSubmission::class, 'user_id', 'id');
    }

      /**
     * Convert gender id to sting
     * @param $id
     * @return string
     */
    public static function getGender ($id) : string
    {
        $gender = null;

        switch ($id){
            case 1:
                $gender = 'Male';
                break;
            case 2:
                $gender = 'Female';
                break;
            default:
                $gender = "";
        }

        return $gender ;

    }
}
