<?php

namespace Tests\Feature;

use App\Http\Resources\CountryCityResource;
use App\Models\Occupation;
use App\Models\Religion;
use App\Models\StudyLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateProfileInitialInfoTest extends TestCase
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
     * Candidate profile initial data response test
     * @return void
     */
    public function test_candidate_profile_initial_data_response_test()
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
        ])->get('/api/v1/candidate/initial-info');

        $this->assertApiSuccess();

        $this->response->assertJsonStructure([
            'data' => [
                'countries'=>[],
                'user'=>[],
                'studylevels'=>[],
                'religions'=>[],
                'occupations'=>[]
            ]
        ]);

        $studyLevels = StudyLevel::orderBy('name')->get();
        $studyLevels = $studyLevels->toArray();

        $religions = Religion::where('status', 1)->orderBy('name')->get();
        $religions = $religions->toArray();

        $occupations = Occupation::pluck('name', 'id');
        $occupations = $occupations->toArray();

        $this->assertModelData($studyLevels,$this->response['data']['studylevels']);
        $this->assertModelData($religions,$this->response['data']['religions']);
        $this->assertModelData($occupations,$this->response['data']['occupations']);
    }
}
