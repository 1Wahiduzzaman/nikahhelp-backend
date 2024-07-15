<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CandidateRatingApiTest extends TestCase
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
        $this->token = JWTAuth::attempt($this->user);
    }

    /**
     * Candidate rating set data test
     * @return void
     */
    public function test_candidate_rating_set_data_test()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/preference-rating');

        $this->assertApiSuccess();
    }
}
