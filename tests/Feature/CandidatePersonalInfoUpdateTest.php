<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidatePersonalInfoUpdateTest extends TestCase
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
        $this->token = \JWTAuth::attempt($this->user);
    }

    /**
     * Personal Date of birth is required Test
     * @return void
     */
    public function test_candidate_dob_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['dob' => ['The date of birth field is required.']]);
    }

    /**
     * Personal date of birth must be valid date format Test
     * @return void
     */
    public function test_candidate_dob_date_format_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[
            'dob'=> 'False string'
        ]);

        $this->response->assertJsonMissing(['dob' => ['The date of birth field is required.']]);
        $this->response->assertJsonFragment(['dob' => ['The date of birth should be a valid date.']]);

    }

    /**
     * Personal date of birth must be valid date format Test
     * @return void
     */
    public function test_candidate_dob_date_must_before_today_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[
            'dob'=> Carbon::now()
        ]);

        $this->response->assertJsonMissing(['dob' => ['The date of birth field is required.']]);
        $this->response->assertJsonMissing(['dob' => ['The date of birth should be a valid date.']]);
        $this->response->assertJsonFragment(['dob' => ['The date of birth should be a valid date before today.']]);

    }

    /**
     * Per gender required Test
     * @return void
     */
    public function test_candidate_per_gender_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_gender' => ['The per gender field is required.']]);
    }

    /**
     * Per height required Test
     * @return void
     */
    public function test_candidate_per_height_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_height' => ['The height field is required.']]);
    }

    /**
     * Per employment status required Test
     * @return void
     */
    public function test_candidate_per_employment_status_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_employment_status' => ['The employment status field is required.']]);
    }

    /**
     * Per education level id required Test
     * @return void
     */
    public function test_candidate_per_education_level_id_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_education_level_id' => ['The education level id field is required.']]);
    }

    /**
     * Per religion id required Test
     * @return void
     */
    public function test_candidate_per_religion_id_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_religion_id' => ['The religion field is required.']]);
    }

    /**
     * Per ethnicity required Test
     * @return void
     */
    public function test_candidate_per_ethnicity_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_ethnicity' => ['The ethnicity field is required.']]);
    }

    /**
     * Per nationality required Test
     * @return void
     */
    public function test_candidate_per_nationality_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_nationality' => ['The nationality field is required.']]);
    }

    /**
     * Per country of birth required Test
     * @return void
     */
    public function test_candidate_per_country_of_birth_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_country_of_birth' => ['The country of birth field is required.']]);
    }

    /**
     * Per current residence country required Test
     * @return void
     */
    public function test_candidate_current_residence_country_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_current_residence_country' => ['The per current residence country field is required.']]);
    }

    /**
     * Per current residence city required Test
     * @return void
     */
    public function test_candidate_current_residence_city_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_current_residence_city' => ['The per current residence city field is required.']]);
    }

    /**
     * Per permanent country required Test
     * @return void
     */
    public function test_candidate_permanent_country_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_permanent_country' => ['The per permanent country field is required.']]);
    }

    /**
     * Per permanent country required Test
     * @return void
     */
    public function test_candidate_permanent_city_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_permanent_city' => ['The per permanent city field is required.']]);
    }

    /**
     * Per permanent post code required Test
     * @return void
     */
    public function test_candidate_permanent_post_code_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_permanent_post_code' => ['The per permanent post code field is required.']]);
    }

    /**
     * Per permanent address required Test
     * @return void
     */
    public function test_candidate_permanent_address_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_permanent_address' => ['The per permanent address field is required.']]);
    }

    /**
     * Per willing to relocate required Test
     * @return void
     */
    public function test_candidate_per_willing_to_relocate_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_willing_to_relocate' => ['The willing to relocate field is required.']]);
    }

    /**
     * Per smoker required Test
     * @return void
     */
    public function test_candidate_per_smoker_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_smoker' => ['The smoker field is required.']]);
    }

    /**
     * Per language speak required Test
     * @return void
     */
    public function test_candidate_per_language_speak_is_required()
    {
        $candidateInfo = CandidateInformation::factory()->create();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$candidateInfo->id,[]);

        $this->response->assertJsonFragment(['per_language_speak' => ['The language speak field is required.']]);
    }

    /**
     * Person update information Test
     * @return void
     */
    public function test_update_candidate_personal_info()
    {
        // edo continue after fix route

        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $data =[
            'dob' => Carbon::now()->subDays(1),
            'mobile_number'=> '01723659050',
            'mobile_country_code'=> 'BD',
            'per_telephone_no'=> Null,
            'per_gender'=> 1,
            'per_height'=> 5.7,
            'per_employment_status'=> 'High Status',
            'per_education_level_id'=> 1,
            'per_religion_id'=> 1,
            'per_ethnicity'=> 'very good',
            'per_mother_tongue'=> Null,
            'per_nationality'=> 1,
            'per_country_of_birth'=> 1,
            'per_current_residence_country'=> 'Bangladesh',
            'per_current_residence_city'=> 'Dhaka',
            'per_permanent_country'=>'Bangladesh',
            'per_permanent_city'=>'Dhaka',
            'per_permanent_post_code'=>'1230',
            'per_permanent_address'=>'Mirpur',
            'per_marital_status'=> '1',
            'per_have_children'=> '1',
            'per_children'=> [],
            'per_currently_living_with'=> Null,
            'per_willing_to_relocate'=> '1',
            'per_smoker'=> '1',
            'per_language_speak'=> '1',
            'per_hobbies_interests'=> Null,
            'per_food_cuisine_like'=> Null,
            'per_things_enjoy'=> Null,
            'per_thankfull_for'=> Null,
            'per_about'=> Null,
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/personal-info/'.$userInfo['data']['user_id'],$data);

        $this->assertApiSuccess();
        $this->assertEquals($data['mobile_number'],$this->response['data']['contact']['mobile_number']);

    }
}
