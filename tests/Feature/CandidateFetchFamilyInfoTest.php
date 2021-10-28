<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateFetchFamilyInfoTest extends TestCase
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
        $user = User::where('email',$this->user['email'])->first();
        $data = [
            "user_id"=>$user->id,
            "fi_father_name"=>'Mr. Father',
            "fi_father_profession"=>'Business',
            "fi_mother_name"=>'Mrs. Mother',
            "fi_mother_profession"=>'Doctor',
            "fi_siblings_desc"=>'No one',
            "fi_country_of_origin"=>'Bangladesh',
            "fi_family_info"=>'Very Good',
        ];

        CandidateInformation::create($data);


        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->get('/api/v1/candidate/family-info?uid='.$user->id);

        $this->assertApiSuccess();
        $this->assertModelData($data, $this->response['data']);
    }

}
