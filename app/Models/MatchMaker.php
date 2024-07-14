<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MatchMaker
 * @package App\Models
 * @version June 30, 2021, 11:24 am UTC
 *
 */
class MatchMaker extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'match_makers';


//    protected $dates = ['deleted_at'];



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

        // Business information
        'capacity',
        'company_or_other',
        'occupation',
        'match_maker_duration',
        'match_qt',
        'match_per_county',
        'match_community',
        'have_previous_experience',
        'can_share_last_three_match',
        'match_one',
        'match_two',
        'match_three',
// end
        'per_avatar_url',
        'per_main_image_url',
        'anybody_can_see',
        'only_team_can_see',
        'team_connection_can_see',
        'is_agree',
        'data_input_status',
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


}
