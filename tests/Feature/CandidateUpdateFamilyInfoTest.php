<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateUpdateFamilyInfoTest extends TestCase
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
     * Candidate father name type Test
     * @return void
     */
    public function test_candidate_father_name_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'father_name'=> 512425
        ]);

        $this->response->assertJsonMissing(['father_name' => ['Father name is required.']]);
        $this->response->assertJsonFragment(['father_name' => ['Father name must be a string.']]);
    }

    /**
     * Candidate father name length validation Test
     * @return void
     */
    public function test_candidate_father_name_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'father_name'=> str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['father_name' => ['Father name is required.']]);
        $this->response->assertJsonMissing(['father_name' => ['Father name must be a string.']]);
        $this->response->assertJsonFragment(['father_name' => ['Father name length can not be more than 255 characters.']]);
    }

    /**
     * Candidate father profession type Test
     * @return void
     */
    public function test_candidate_father_profession_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'father_profession'=> 512425
        ]);
        $this->response->assertJsonFragment(['father_profession' => ['The father profession must be a string.']]);
    }

    /**
     * Candidate father_profession length validation Test
     * @return void
     */
    public function test_candidate_father_profession_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'father_profession'=> str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['father_profession' => ['The father profession must be a string.']]);
        $this->response->assertJsonFragment(['father_profession' => ['Father profession length can not be more than 255 characters.']]);
    }

    /**
     * Candidate mother name type Test
     * @return void
     */
    public function test_candidate_mother_name_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'mother_name'=> 512425
        ]);

        $this->response->assertJsonMissing(['mother_name' => ['Mother name is required.']]);
        $this->response->assertJsonFragment(['mother_name' => ['The mother name must be a string.']]);
    }

    /**
     * Candidate mother name length validation Test
     * @return void
     */
    public function test_candidate_mother_name_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'mother_name'=> str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['mother_name' => ['Mother name is required.']]);
        $this->response->assertJsonMissing(['mother_name' => ['Mother name must be a string.']]);
        $this->response->assertJsonFragment(['mother_name' => ['The mother name must not be greater than 255 characters.']]);
    }

    /**
     * Candidate mother profession type Test
     * @return void
     */
    public function test_candidate_mother_profession_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'mother_profession'=> 512425
        ]);
        $this->response->assertJsonFragment(['mother_profession' => ['The mother profession must be a string.']]);
    }

    /**
     * Candidate mother_profession length validation Test
     * @return void
     */
    public function test_candidate_mother_profession_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'mother_profession'=> str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['mother_profession' => ['The mother profession must be a string.']]);
        $this->response->assertJsonFragment(['mother_profession' => ['Mother profession length can not be more than 255 characters.']]);
    }

    /**
     * Candidate siblings desc type Test
     * @return void
     */
    public function test_candidate_siblings_desc_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'siblings_desc'=> 512425
        ]);
        $this->response->assertJsonFragment(['siblings_desc' => ['The siblings desc must be a string.']]);
    }

    /**
     * Candidate siblings desc length validation Test
     * @return void
     */
    public function test_candidate_siblings_desc_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'siblings_desc'=> str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['siblings_desc' => ['The siblings desc must be a string.']]);
        $this->response->assertJsonFragment(['siblings_desc' => ['Siblings Description length can not be more than 255 characters.']]);
    }

    /**
     * Candidate country of origin type Test
     * @return void
     */
    public function test_candidate_country_of_origin_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'country_of_origin'=> 512425
        ]);

        $this->response->assertJsonMissing(['country_of_origin' => ['Country of origin is required.']]);
        $this->response->assertJsonFragment(['country_of_origin' => ['The country of origin must be a string.']]);
    }

    /**
     * Candidate family info type Test
     * @return void
     */
    public function test_candidate_family_info_type_string_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'family_info'=> 512425
        ]);
        $this->response->assertJsonFragment(['family_info' => ['The family info must be a string.']]);
    }

    /**
     * Candidate family info length validation Test
     * @return void
     */
    public function test_candidate_family_info_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'family_info'=> str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['family_info' => ['The family info must be a string.']]);
        $this->response->assertJsonFragment(['family_info' => ['Family info length can not be more than 255 characters.']]);
    }

    /**
     * Candidate family is publish type Test
     * @return void
     */
    public function test_candidate_family_is_publish_type_boolean_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info',[
            'is_publish'=> 512425
        ]);

        $this->response->assertJsonFragment(['is_publish' => ['Is publish flag needs to have a boolean value.']]);
    }

    /**
     * Candidate family info Update Test
     * @return void
     */
    public function test_candidate_family_info_update()
    {
        $user = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
            'father_name'=>'Old father name ',
            'father_profession'=>'Old father profession',
            'mother_name'=>'Old mother name',
            'mother_profession'=>'Old mother profession',
            'siblings_desc'=>'Old siblings desc',
            'country_of_origin'=>'Old country of origin',
            'family_info'=>'Old family info',
            'is_publish'=>'Old is publish',
        ]);

        $data = [
            'uid' => $user['data']['id'],
            'father_name'=>'New father name',
            'father_profession'=>'New father profession',
            'mother_name'=>'New mother name',
            'mother_profession'=>'New mother profession',
            'siblings_desc'=>'New siblings desc',
            'country_of_origin'=>'New country of origin',
            'family_info'=>'New family info',
            'is_publish'=> 1,
        ];

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->patch('/api/v1/candidate/family-info', $data);


        $data = [
            'fi_father_name'=>'New father name',
            'fi_father_profession'=>'New father profession',
            'fi_mother_name'=>'New mother name',
            'fi_mother_profession'=>'New mother profession',
            'fi_siblings_desc'=>'New siblings desc',
            'fi_country_of_origin'=>'New country of origin',
            'fi_family_info'=>'New family info',
        ];

        $this->assertApiSuccess();
        $this->assertModelData($data,$this->response['data']);

    }
}
