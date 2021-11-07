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

        /* Request with no parameter to check each and every input field with null value can be accepted*/
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-generalinformation');


        $this->assertApiSuccess();

    }

}
