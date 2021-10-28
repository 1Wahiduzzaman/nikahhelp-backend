<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateEssentialInfoTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

    private $response;

    /**
     * Make seed in database
     * * Attempt login and Generate Auth token
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
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['dob' => ['The date of birth field is required.']]);
    }

    /**
     * Personal date of birth must be valid date format Test
     * @return void
     */
    public function test_candidate_dob_date_format_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', [
            'dob' => 'False string'
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
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', [
            'dob' => Carbon::now()
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
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['per_gender' => ['The per gender field is required.']]);
    }

    /**
     * Per height required Test
     * @return void
     */
    public function test_candidate_per_height_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['per_height' => ['The height field is required.']]);
    }

    /**
     * Per employment status required Test
     * @return void
     */
    public function test_candidate_per_employment_status_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['per_employment_status' => ['The employment status field is required.']]);
    }

    /**
     * Per education level id required Test
     * @return void
     */
    public function test_candidate_per_education_level_id_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['per_education_level_id' => ['The education level id field is required.']]);
    }

    /**
     * Per religion id required Test
     * @return void
     */
    public function test_candidate_per_religion_id_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['per_religion_id' => ['The religion field is required.']]);
    }

    /**
     * Per occupation id required Test
     * @return void
     */
    public function test_candidate_per_occupation_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', []);

        $this->response->assertJsonFragment(['per_occupation' => ['The occupation field is required.']]);
    }

    /**
     * Per Essential info update Test
     * @return void
     */
    public function test_update_candidate_essential_info_update()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $data = [
            'dob' => Carbon::now()->subDays(1),
            'per_gender' => 1,
            'per_height' => 5.7,
            'per_employment_status' => 'High Status',
            'per_education_level_id' => 1,
            'per_religion_id' => 1,
            'per_occupation' => 'Business',
            'per_telephone_no'=> '01723659050'
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation', $data);


        $responseData = $this->response['data']['essential'];
        unset($data['dob']);
        unset($responseData['dob']);

        $this->assertApiSuccess();
        $this->assertEquals($data, $responseData);

    }

}
