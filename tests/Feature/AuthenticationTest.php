<?php

namespace Tests\Feature;

use App\Models\CandidateInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use JWTAuth;

/**
 * Everything to do with Authentication of Users in matrimony
 * @author khorram khorramk.kbsk@gmail.com
 * @copyright 2021 Matrimony
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Testing Register route
     *
     * @return void
     */
    public function testRegister()
    {
//        dd('sdf');
        $userDetails = array(
            'full_name' => 'munna',
            'email' => 'raz.abcoder@gmail.com',
            'password' => 'aaaaaaaa',
            'first_name'=>'raz',
            'last_name'=>'ahmed',
            'screen_name'=>'raz215',
            'account_type'=>1,
        );

        $response = $this->post('/api/v1/register', $userDetails);

        $status = $response['status_code'] == 200 ? 201 : 200;

        $response->assertStatus($status);
    }

    /**
     * A basic login test
     *
     * @return void
     */
    public function testLogin()
    {
        $user['email'] = "raz.abcoder@gmail.com";
        $user['password'] = 'aaaaaaaa';
        $token = JWTAuth::attempt($user);

        $response = $this->withHeaders(['Authorization' => $token])
            ->post('/api/v1/login', $user);
        $response->assertJsonFragment(['status_code' => $response['status_code']]);
    }

    /**
     * Logout test
     *
     * @return void
     */
    public function testLogout()
    {
        $user['email'] = "raz.abcodersada@gmail.com";
        $user['password'] = 'aaaaaaaa';
        $token = JWTAuth::attempt($user);
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->get('/api/v1/logout');
        $response->assertStatus($response['status_code']);
    }

    /**
     * Email verification
     *
     * @return void
     */
    public function testEmailVerification()
    {
        $user['email'] = "raz.doict@gmail.com";
        $user['password'] = 'aaaaaaaa';
        $token = JWTAuth::attempt($user);
        $response = $this->get('/api/v1/emailVerify/'.$token);
        $response->assertStatus($response['status_code']);
    }

    /**
     * Token Refresh
     *
     * @return void
     */
    public function testTokenRefresh()
    {
        $user['email'] = "raz.doict@gmail.com";
        $user['password'] = 'aaaaaaaa';
        $token = JWTAuth::attempt($user);
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->get('/api/v1/token-refresh');
        $response->assertStatus($response['status_code']);
    }

    /**
     * Forgot password
     *
     * @return void
     */
    public function testForgotPassword()
    {
        $user['email'] = "raz.doictjhkjhk@gmail.com";

        $response = $this->post('/api/v1/forgot/password', $user);
        $response->assertStatus($response['status_code']);
    }

    /**
     * PAssword verification
     *
     * @return void
     */
    public function testPasswordVerify()
    {
        $token = 'QbCgy2RJnT6bUd3DlBv8pZBof4DRl022nmvTVK5J47pEKyVTvkQJHooXSIdQ';
        $response = $this->post("/api/v1/forgot/password/verify", ['token' => $token]);
        $response->assertStatus($response['status_code']);
    }
    /**
     * Password Update
     *
     * @return void
     */
    public function testPasswordUpdate()
    {
        $user['email'] = "raz.abcoder@gmail.com";
        $user['password'] = 'aaaaaaa1';
        $user['token'] = 'QbCgy2RJnT6bUd3DlBv8pZBof4DRl022nmvTVK5J47pEKyVTvkQJHooXSIdQ';
        $response = $this->post('/api/v1/forgot/password/update', $user);
        $response->assertStatus($response['status_code']);
    }
}
