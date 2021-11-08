<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateMoreInfoUpdateTest extends TestCase
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
     * Candidate marital status type Test
     * @return void
     */
    public function test_candidate_marital_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_marital_status' => 512
        ]);

        $this->response->assertJsonFragment(['per_marital_status' => ['The per marital status must be a string.']]);

    }

    /**
     * Candidate per_currently_living_with must be string Test
     * @return void
     */
    public function test_candidate_currently_living_with_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_currently_living_with'=> 512
        ]);

        $this->response->assertJsonFragment(['per_currently_living_with' => ['The per currently living with must be a string.']]);
    }

    /**
     * Candidate per_smoker must be string Test
     * @return void
     */
    public function test_candidate_per_smoker_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_smoker'=> 512
        ]);

        $this->response->assertJsonFragment(['per_smoker' => ['The per smoker must be a string.']]);
    }

    /**
     * Candidate per_language_speak must be string Test
     * @return void
     */
    public function test_candidate_language_speak_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_language_speak'=> 512
        ]);

        $this->response->assertJsonFragment(['per_language_speak' => ['The per language speak must be a string.']]);
    }

    /**
     * Candidate per_hobbies_interests must be string Test
     * @return void
     */
    public function test_candidate_hobbies_interests_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_hobbies_interests'=> 512
        ]);

        $this->response->assertJsonFragment(['per_hobbies_interests' => ['The per hobbies interests must be a string.']]);
    }

    /**
     * Candidate per_food_cuisine_like must be string Test
     * @return void
     */
    public function test_candidate_food_cuisine_like_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_food_cuisine_like'=> 512
        ]);

        $this->response->assertJsonFragment(['per_food_cuisine_like' => ['The per food cuisine like must be a string.']]);
    }

    /**
     * Candidate per_things_enjoy must be string Test
     * @return void
     */
    public function test_candidate_things_enjoy_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_things_enjoy'=> 512
        ]);

        $this->response->assertJsonFragment(['per_things_enjoy' => ['The per things enjoy must be a string.']]);
    }

    /**
     * Candidate per_thankfull_for must be string Test
     * @return void
     */
    public function test_candidate_thankfull_for_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_thankfull_for'=> 512
        ]);

        $this->response->assertJsonFragment(['per_thankfull_for' => ['The per thankfull for must be a string.']]);
    }

    /**
     * Candidate per_about must be string Test
     * @return void
     */
    public function test_candidate_about_must_be_string()
    {
        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',[
            'per_about'=> 512
        ]);

        $this->response->assertJsonFragment(['per_about' => ['The per about must be a string.']]);
    }

    /**
     * Candidate More information update Test
     * @return void
     */
    public function test_update_candidate_more_info()
    {

        $userInfo = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $data =[
            'per_marital_status' => 'single',
            'per_have_children'=> '0',
            'per_children'=> [],
            'per_currently_living_with'=> 'Uncle',
            'per_willing_to_relocate'=> 1,
            'per_smoker'=> '0',
            'per_language_speak'=> 'Bangla',
            'per_hobbies_interests'=> 'Traveling',
            'per_food_cuisine_like'=> 'Ice-cream',
            'per_things_enjoy'=> 'Coding',
            'per_thankfull_for'=> Null,
            'per_about'=> 'I\'m very good boy',
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-more-about',$data);

        $responseData = $this->response['data']['more_about'];

        $this->assertApiSuccess();
        $this->assertEquals($data,$responseData);
    }
}
