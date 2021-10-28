<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class CandidateContactInfoUpdateTest extends TestCase
{
    use RefreshDatabase, ApiTestTrait;

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
     * Person email required Test
     * @return void
     */
    public function test_candidate_per_email_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_email' => ['The per email field is required.']]);
    }

    /**
     * Person email length validation Test
     * @return void
     */
    public function test_candidate_per_email_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_email' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_email' => ['The per email field is required.']]);
        $this->response->assertJsonFragment(['per_email' => ['The per email must not be greater than 255 characters.']]);
    }

    /**
     * Person mobile number required Test
     * @return void
     */
    public function test_candidate_mobile_number_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['mobile_number' => ['The mobile number field is required.']]);
    }

    /**
     * Person mobile number length validation Test
     * @return void
     */
    public function test_candidate_per_mobile_number_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'mobile_number' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['mobile_number' => ['The mobile number field is required.']]);
        $this->response->assertJsonFragment(['mobile_number' => ['The mobile number must not be greater than 255 characters.']]);
    }

    /**
     * Person current residence country required Test
     * @return void
     */
    public function test_candidate_per_current_residence_country_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_current_residence_country' => ['The per current residence country field is required.']]);
    }

    /**
     * Person current residence country length validation Test
     * @return void
     */
    public function test_candidate_per_current_residence_country_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_current_residence_country' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_current_residence_country' => ['The per current residence country field is required.']]);
        $this->response->assertJsonFragment(['per_current_residence_country' => ['The per current residence country must not be greater than 255 characters.']]);
    }

    /**
     * Person current residence city required Test
     * @return void
     */
    public function test_candidate_per_current_residence_city_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_current_residence_city' => ['The per current residence city field is required.']]);
    }

    /**
     * Person current residence city length validation Test
     * @return void
     */
    public function test_candidate_per_current_residence_city_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_current_residence_city' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_current_residence_city' => ['The per current residence city field is required.']]);
        $this->response->assertJsonFragment(['per_current_residence_city' => ['The per current residence city must not be greater than 255 characters.']]);
    }

    /**
     * Person permanent country required Test
     * @return void
     */
    public function test_candidate_per_permanent_country_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_permanent_country' => ['The per permanent country field is required.']]);
    }

    /**
     * Person permanent country length validation Test
     * @return void
     */
    public function test_candidate_per_permanent_country_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_permanent_country' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_permanent_country' => ['The per permanent country field is required.']]);
        $this->response->assertJsonFragment(['per_permanent_country' => ['The per permanent country must not be greater than 255 characters.']]);
    }

    /**
     * Person permanent city required Test
     * @return void
     */
    public function test_candidate_per_permanent_city_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_permanent_city' => ['The per permanent city field is required.']]);
    }

    /**
     * Person per_permanent_city length validation Test
     * @return void
     */
    public function test_candidate_per_permanent_city_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_permanent_city' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_permanent_city' => ['The per permanent city field is required.']]);
        $this->response->assertJsonFragment(['per_permanent_city' => ['The per permanent city must not be greater than 255 characters.']]);
    }

    /**
     * Person county required Test
     * @return void
     */
    public function test_candidate_per_county_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_county' => ['The per county field is required.']]);
    }

    /**
     * Person county length validation Test
     * @return void
     */
    public function test_candidate_per_county_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_county' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_county' => ['The per county field is required.']]);
        $this->response->assertJsonFragment(['per_county' => ['The per county must not be greater than 255 characters.']]);
    }

    /**
     * Person permanent post code required Test
     * @return void
     */
    public function test_candidate_per_permanent_post_code_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_permanent_post_code' => ['The per permanent post code field is required.']]);
    }

    /**
     * Person permanent post code length validation Test
     * @return void
     */
    public function test_candidate_per_permanent_post_code_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_permanent_post_code' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_permanent_post_code' => ['The per permanent post code field is required.']]);
        $this->response->assertJsonFragment(['per_permanent_post_code' => ['The per permanent post code must not be greater than 255 characters.']]);
    }

    /**
     * Person permanent address required Test
     * @return void
     */
    public function test_candidate_per_permanent_address_required()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', []);

        $this->response->assertJsonFragment(['per_permanent_address' => ['The per permanent address field is required.']]);
    }

    /**
     * Person permanent address length validation Test
     * @return void
     */
    public function test_candidate_per_permanent_address_length_validation()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', [
            'per_permanent_address' => str_repeat("Hello World , ", 30)
        ]);

        $this->response->assertJsonMissing(['per_permanent_address' => ['The per permanent address field is required.']]);
        $this->response->assertJsonFragment(['per_permanent_address' => ['The per permanent address must not be greater than 255 characters.']]);
    }

    /**
     * Person contact update Test
     * @return void
     */
    public function test_candidate_contact_update()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->post('/api/v1/candidate/create',[
            'first_name'=>'Rabbial',
            'last_name'=>' Anower',
            'screen_name'=>'rabbilarabbi',
        ]);

        $data = [
            'per_email' => 'email@g.com',
            'mobile_number' => '01723659050',
            'per_current_residence_country' => 'Bangladesh',
            'per_current_residence_city' => 'Dhaka',
            'per_permanent_country' => 'Bangladesh',
            'per_permanent_city' => 'Dhaka',
            'per_county' => 'Bangladesh',
            'per_permanent_post_code' => '1230',
            'per_permanent_address' => 'Dhaka',
        ];
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->POST('/api/v1/candidate/personal-cotactinformation', $data);

        $this->assertApiSuccess();
        $this->assertEquals($data['mobile_number'], $this->response['data']['contact']['mobile_number']);


    }
}
