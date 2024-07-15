<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateBasicInfoTest extends TestCase
{
    use RefreshDatabase,ApiTestTrait;

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
     * Personal basic info test
     * @return void
     */
    public function test_candidate_basic_info()
    {
        $candidateInfo = CandidateInformation::factory()->create();
        $data = [
            "first_name" => $candidateInfo->first_name,
            "last_name" => $candidateInfo->last_name,
            "screen_name" => $candidateInfo->screen_name,
            'data_input_status'=>1,
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/basic-info/'.$candidateInfo->id,$data);

        $this->assertApiSuccess();
        $this->assertModelData($candidateInfo->toArray(),$this->response['data']);
    }

}
