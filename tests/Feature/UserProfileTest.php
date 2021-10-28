<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestTrait;
use Tests\TestCase;

class UserProfileTest extends TestCase
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
     * User Profile Info Test
     * @return void
     */
    public function test_user_profile_info()
    {
        $user = User::where('email',$this->user['email'])->first();

        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->get('/api/v1/user-profile?user_id='.$user->id);

        $this->assertApiSuccess();
    }
}
