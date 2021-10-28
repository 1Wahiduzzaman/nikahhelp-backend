<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateGeneralInfoUpdateTest extends TestCase
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
     * Per ethnicity required Test
     * @return void
     */
    public function test_candidate_per_ethnicity_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation', []);

        $this->response->assertJsonFragment(['per_ethnicity' => ['The ethnicity field is required.']]);
    }

    /**
     * Per mother tongue must be string Test
     * @return void
     */
    public function test_candidate_per_mother_tongue_must_be_string()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation', [
            'per_mother_tongue' => 0
        ]);

        $this->response->assertJsonFragment(['per_mother_tongue' => ['The per mother tongue must be a string.']]);
    }

    /**
     * Per nationality required Test
     * @return void
     */
    public function test_candidate_per_nationality_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation', []);

        $this->response->assertJsonFragment(['per_nationality' => ['The nationality field is required.']]);
    }

    /**
     * Per country of birth required Test
     * @return void
     */
    public function test_candidate_per_country_of_birth_is_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation', []);

        $this->response->assertJsonFragment(['per_country_of_birth' => ['The country of birth field is required.']]);
    }

    /**
     * Per health condition must be string Test
     * @return void
     */
    public function test_candidate_per_health_condition_must_be_string()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation', [
            'per_health_condition' => 0
        ]);

        $this->response->assertJsonFragment(['per_health_condition' => ['The per health condition must be a string.']]);
    }

    /**
     * Per General info update Test
     * @return void
     */
    public function test_update_candidate_general_info_update()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $data = [
            'per_ethnicity' => 'very good',
            'per_mother_tongue' => Null,
            'per_nationality' => 1,
            'per_country_of_birth' => 1,
            'per_health_condition' => Null,
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation', $data);

        $responseData = $this->response['data']['general'];

        $this->assertApiSuccess();
        $this->assertEquals($data, $responseData);

    }

}
