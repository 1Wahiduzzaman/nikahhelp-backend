<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class CandidateInformation extends Model
{
    use HasFactory, ImageTrait;

    /**
     * @var string
     */
    public $table = 'candidate_information';

    /**
     * @var bool
     */
    public $timestamps = false;

    public const RATE_TO_STRING = [
        1 => 'Not Important',
        2 => 'Quite Important',
        3 => 'Important',
        4 => 'Very Important',
        5 => 'Extremely Important',
    ];

    public const PREFERENCE_INFO = [
        'pre_partner_age_min',
        'pre_partner_age_max',
        'pre_height_min',
        'pre_height_max',
        'pre_has_country_allow_preference',
        'pre_has_country_disallow_preference',
        'pre_partner_religion_id',
        'pre_ethnicities',
        'pre_study_level_id',
        'pre_employment_status',
        'pre_occupation',
        'pre_preferred_divorcee',
        'pre_preferred_divorcee_child',
        'pre_other_preference',
        'pre_description',
        "pre_pros_part_status",

        'pre_strength_of_character_rate',
        'pre_look_and_appearance_rate',
        'pre_religiosity_or_faith_rate',
        'pre_manners_socialskill_ethics_rate',
        'pre_emotional_maturity_rate',
        'pre_good_listener_rate',
        'pre_good_talker_rate',
        'pre_wiling_to_learn_rate',
        'pre_family_social_status_rate',
        'pre_employment_wealth_rate',
        'pre_education_rate',
        'pre_things_important_status',
    ];
    public const PERSONAL_INFO = [
        'dob',
        'mobile_number',
        'mobile_country_code',
        'per_telephone_no',
        'per_gender',
        'per_height',
        'per_employment_status',
        'per_education_level_id',
        'per_religion_id',
        'per_ethnicity',
        'per_mother_tongue',
        'per_nationality',
        'per_country_of_birth',
        'per_current_residence_country',
        'per_current_residence_city',
        'per_permanent_country',
        'per_permanent_city',
        'per_permanent_post_code',
        'per_permanent_address',
        'per_marital_status',
        'per_have_children',
        'per_children',
        'per_currently_living_with',
        'per_willing_to_relocate',
        'per_smoker',
        'per_language_speak',
        'per_hobbies_interests',
        'per_food_cuisine_like',
        'per_things_enjoy',
        'per_thankfull_for',
        'per_about',
    ];

    public const BASIC_INFO = [
        'first_name',
        'last_name',
        'screen_name',
        'data_input_status',
    ];

    public const PERSONAL_ESSENTIAL_INFO = [
        'dob',
        'per_telephone_no',
        'per_gender',
        'per_height',
        'per_employment_status',
        'per_occupation',
        'per_education_level_id',
        'per_religion_id'
    ];
    public const PERSONAL_GENERAL_INFO = [
        'per_ethnicity',
        'per_mother_tongue',
        'per_nationality',
        'per_country_of_birth',
        'per_health_condition',
    ];

    public const PERSONAL_CONTACT_INFO = [
        'address_1',
        'address_2',
        'per_email',
        'per_current_residence_country',
        'per_current_residence_city',
        'per_permanent_country',
        'per_permanent_city',
        'per_county',
        'per_permanent_post_code',
        'per_permanent_address',
        'mobile_number',
        'mobile_country_code'
    ];
    public const PERSONAL_MOREABOUT_INFO = [
        'per_marital_status',
        'per_have_children',
        'per_children',
        'per_currently_living_with',
        'per_willing_to_relocate',
        'per_smoker',
        'per_language_speak',
        'per_hobbies_interests',
        'per_food_cuisine_like',
        'per_things_enjoy',
        'per_thankfull_for',
        'per_about',
        'per_improve_myself',
        'per_additional_info_text',
        'per_additional_info_doc',
        'per_additional_info_doc_title',
    ];

    public const PERSONAL_VERIFICATION_INFO = [
        'ver_country',
        'ver_city_id',
        'ver_document_type',
        'ver_image_front',
        'ver_image_back',
        'ver_recommences_title',
        'ver_recommences_first_name',
        'ver_recommences_last_name',
        'ver_recommences_occupation',
        'ver_recommences_address',
        'ver_recommences_mobile_no',
        'ver_status',
    ];

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'screen_name',
        'dob',
        'mobile_number',
        'mobile_country_code',
        'per_telephone_no',
        'per_gender',
        'per_height',
        'per_employment_status',
        'per_education_level_id',
        'per_religion_id',
        'per_ethnicity',
        'per_mother_tongue',
        'per_nationality',
        'per_country_of_birth',
        'per_health_condition',
        'per_current_residence',
        'per_occupation',
        'per_email',
        'per_current_residence_country',
        'per_current_residence_city',
        'per_permanent_country',
        'per_permanent_city',
        'per_county',
        'per_permanent_post_code',
        'per_permanent_address',

        'per_address',
        'per_marital_status',
        'per_have_children',
        'per_children',
        'per_currently_living_with',
        'per_willing_to_relocate',
        'per_smoker',
        'per_language_speak',
        'per_hobbies_interests',
        'per_food_cuisine_like',
        'per_things_enjoy',
        'per_thankfull_for',
        'per_about',
        'per_improve_myself',
        'per_additional_info_text',
        'per_additional_info_doc',
        'per_additional_info_doc_title',

        // Preference
        'pre_partner_age_min',
        'pre_partner_age_max',
        'pre_height_min',
        'pre_height_max',
        'pre_has_country_allow_preference',
        'pre_has_country_disallow_preference',
        'pre_partner_religion_id',
        'pre_ethnicities',
        'pre_study_level_id',
        'pre_employment_status',
        'pre_occupation',
        'pre_preferred_divorcee',
        'pre_preferred_divorcee_child',
        'pre_other_preference',
        'pre_description',

        "pre_pros_part_status",
        'pre_strength_of_character_rate',
        'pre_look_and_appearance_rate',
        'pre_religiosity_or_faith_rate',
        'pre_manners_socialskill_ethics_rate',
        'pre_emotional_maturity_rate',
        'pre_good_listener_rate',
        'pre_good_talker_rate',
        'pre_wiling_to_learn_rate',
        'pre_family_social_status_rate',
        'pre_employment_wealth_rate',
        'pre_education_rate',
        'pre_things_important_status',

        "fi_father_name",
        "fi_father_profession",
        "fi_mother_name",
        "fi_mother_profession",
        "fi_siblings_desc",
        "fi_country_of_origin",
        "fi_family_info",

        // Verification
        'ver_country',
        'ver_city_id',
        'ver_document_type',
        'ver_image_front',
        'ver_image_back',
        'ver_recommences_title',
        'ver_recommences_first_name',
        'ver_recommences_last_name',
        'ver_recommences_occupation',
        'ver_recommences_address',
        'ver_recommences_mobile_no',
        'ver_status',

        "is_publish",
        "anybody_can_see",
        "only_team_can_see",
        "team_connection_can_see",
        "per_avatar_url" .
        "per_main_image_url",

        'data_input_status',
        "other_images",
        'address_1',
        'address_2'
    ];

    /**
     * Get the user's children.
     *
     * @param   $value
     * @return mixed
     */
    public function getPerChildrenAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Get the user's children.
     *
     * @param string $value
     * @return void
     */
    public function setPerChildrenAttribute($value)
    {
        $this->attributes['per_children'] = json_encode($value);
    }

    /**
     * The roles that belong to the user.
     */
    public function preferred_countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'candidate_country_user',
            'user_id', 'candidate_pre_country_id', 'user_id'
        )->wherePivot('allow', '=', 1);
    }

    /**
     * The roles that belong to the user.
     */
    public function bloked_countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'candidate_country_user',
            'user_id', 'candidate_pre_country_id', 'user_id'
        )->wherePivot('allow', '=', 0);
    }

    /**
     * The roles that belong to the user.
     */
    public function preferred_cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'candidate_city',
            'user_id', 'city_id', 'user_id'
        )->wherePivot('allow', '=', 1);
    }

    /**
     * The roles that belong to the user.
     */
    public function blocked_cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'candidate_city',
            'user_id', 'city_id', 'user_id'
        )->wherePivot('allow', '=', 0);
    }

    /**
     * The roles that belong to the user.
     */
    public function preferred_nationality(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'candidate_nationality_user',
            'user_id', 'candidate_pre_country_id', 'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getNationality()
    {
        return $this->belongsTo(Country::class, 'per_nationality', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getReligion()
    {
        return $this->belongsTo(Religion::class, 'per_religion_id', 'id');
    }

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
    public function candidateEducationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'per_education_level_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function candidateTeam()
    {
        return $this->hasMany(TeamMember::class,'user_id','user_id')
            ->where('user_type','Candidate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCountryOFBirth()
    {
        return $this->belongsTo(Country::class, 'per_country_of_birth', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCurrentResidenceCountry()
    {
        return $this->belongsTo(Country::class, 'per_current_residence_country', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getPermanentCountry()
    {
        return $this->belongsTo(Country::class, 'per_permanent_country', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getPetnarReligion()
    {
        return $this->belongsTo(Religion::class, 'pre_partner_religions', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getPetnarCountryOFBirth()
    {
        return $this->belongsTo(Religion::class, 'pre_partner_religions', 'id');
    }

    /**
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
     * Return Candidate information block listed by user
     * @return BelongsToMany
     */
    public function blockList()
    {
        return $this->belongsToMany(CandidateInformation::class, 'block_lists', 'block_by', 'user_id','user_id','user_id')->withTimestamps();

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function preEducationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'pre_study_level_id', 'id');

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamConnection()
    {
        return $this->hasMany(TeamConnection::class,'requested_by','user_id')->where('connection_status',1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamMember()
    {
        return $this->hasMany(TeamMember::class,'user_id','user_id');
    }

    public function activeTeams()
    {
        return $this->belongsToMany(Team::class,'team_members','user_id','team_id','user_id','id')->wherePivot('status',1);
    }

    public function getActiveTeamAttribute()
    {
        return $this->activeTeams()->first();
    }

    public function getPerMainImageUrlAttribute($value)
    {

        return $this->getImagePath($value, $this->user_id);
    }

    public function getPerAvatarUrlAttribute($value)
    {
        return $this->getImagePath($value, $this->user_id);
    }


    public function getOtherImagesAttribute($value)
    {
        return $this->getImagePath($value, $this->user_id);
    }

    public function getDownloadableDocAttribute()
    {
        if(Auth::user()->account_type=='10') {
            return $this->per_additional_info_doc ? env('IMAGE_SERVER') . '/' . $this->per_additional_info_doc : '';
        } else {
            $authUserActiveTeam = $this->active_team;
            $candidateActiveTeam = $this->active_team;
            if(!$candidateActiveTeam){
                return null;
            }
            $connectFrom = $authUserActiveTeam->sentRequest->pluck('team_id')->toArray();
            $connectTo = $authUserActiveTeam->receivedRequest->pluck('team_id')->toArray();
            $userConnectList = array_unique(array_merge($connectFrom,$connectTo)) ;
            if(in_array($candidateActiveTeam->team_id,$userConnectList)){
                return $this->per_additional_info_doc;
            }
            return null;
        }
    }

    public function getRepresentativeStatusAttribute()
    {
       return $this->active_team ? (bool)$this->active_team->representativeOfTeamFromUser->filter(function($user){ return $user->account_type > 2; })->count() : false ;
    }

    public function getVerImageFrontAttribute($value)
    {
        return $this->getImagePath($value, $this->user_id);
    }

    public function getVerImageBackAttribute($value)
    {
        return $this->getImagePath($value, $this->user_id);
    }

    public function getPerAdditionalInfoDocAttribute($value)
    {
        return $this->getImagePath($value, $this->user_id);
    }
}
