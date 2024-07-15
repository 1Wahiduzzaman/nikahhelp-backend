<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthenticateTest extends TestCase
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
        $this->token = JWTAuth::attempt($this->user);
    }

    /**
     * User authenticate test
     * @return void
     */
    public function test_user_authenticate_test()
    {
        $this->response = $this->withHeaders([
            'Authorization' => 'Bearer' . $this->token
        ])->get('/api/v1/user');

        $user = User::where('email',$this->user['email'])->get();
        $user = $user->toArray();

        $this->assertApiSuccess();
        $this->assertModelData($user,$this->response['data']);
    }

}
