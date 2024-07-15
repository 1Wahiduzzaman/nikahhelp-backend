<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CandidatePreferenceAboutTest extends TestCase
{
    use RefreshDatabase,ApiTestTrait;

    private $response;

    /**
     * Make seed in database
     * Attempt login and Generate Auth token
     * @return void
     */
    public function setup(): void
    {
        parent::setUp();
        $this->seed();

        $this->user['email'] = 'admin@mail.com';
        $this->user['password'] = '12345678';
        $this->token = JWTAuth::attempt($this->user);
    }

    /**
     * Personal Date of birth is required Test
     * @return void
     */
    public function test_candidate_preference_about_store_test()
    {
        $user = User::where('email','admin@mail.com')->first();
        CandidateInformation::create([
            "user_id" => $user->id,
            "first_name" => 'Mr.jhon',
            "last_name" => 'Doe',
            "screen_name" => 'MR5128',
            'data_input_status'=>1,
        ]);

        /* Must be possible for insert data even though al field are empty
         * It is because the api will be used for each and every key event in any of input field
         * for other input field data might be null
         */
        $candidatePreference = [
            'pre_partner_age_min'=>'',
            'pre_partner_age_max'=>'',
            'pre_height_min'=>'',
            'pre_height_max'=>'',
            'pre_has_country_allow_preference'=>'',
            'preferred_countries'=>'',
            'preferred_cities'=>'',
            'pre_has_country_disallow_preference'=>'',
            'bloked_countries'=>'',
            'blocked_cities'=>'',
            'preferred_nationality'=>'',
            'pre_partner_religion_id'=>'',
            'pre_ethnicities'=>'',
            'pre_study_level_id'=>'',
            'pre_employment_status'=>'',
            'pre_occupation'=>'',
            'pre_preferred_divorcee'=>'',
            'pre_preferred_divorcee_child'=>'',
            'pre_other_preference'=>'',
            'pre_description'=>'',
            'pre_pros_part_status'=>'',
            'pre_strength_of_character_rate'=>'',
            'pre_strength_of_character_rate_string'=>'',
            'pre_look_and_appearance_rate'=>'',
            'pre_look_and_appearance_rate_string'=>'',
            'pre_religiosity_or_faith_rate'=>'',
            'pre_religiosity_or_faith_rate_string'=>'',
            'pre_manners_socialskill_ethics_rate'=>'',
            'pre_manners_socialskill_ethics_rate_string'=>'',
            'pre_emotional_maturity_rate'=>'',
            'pre_emotional_maturity_rate_string'=>'',
            'pre_good_listener_rate'=>'',
            'pre_good_listener_rate_string'=>'',
            'pre_good_talker_rate'=>'',
            'pre_good_talker_rate_string'=>'',
            'pre_wiling_to_learn_rate'=>'',
            'pre_wiling_to_learn_rate_string'=>'',
            'pre_family_social_status_rate'=>'',
            'pre_family_social_status_rate_string'=>'',
            'pre_employment_wealth_rate'=>'',
            'pre_employment_wealth_rate_string'=>'',
            'pre_education_rate'=>'',
            'pre_education_rate_string'=>'',
            'pre_things_important_status'=>'',
            'pre_partner_comes_from'=>[],
            'pre_disallow_preference'=>[]
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/preference-about',$candidatePreference);

        $this->assertApiSuccess();
    }
}
