<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $this->token = JWTAuth::attempt($this->user);
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

        $this->response->assertJsonMissing(['dob' => ['The date of birth should be a valid date.']]);
        $this->response->assertJsonFragment(['dob' => ['The date of birth should be a valid date before today.']]);

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

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/personal-essentialInformation');

        $this->assertApiSuccess();

    }

}
