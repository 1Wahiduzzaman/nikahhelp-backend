<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Everything to with Account Switch endpoints
 * @author khorram khorramk.kbsk@gmail.com
 * @copyright 2021 Matrimony
 *
 */
class AccountSwitchTest extends TestCase
{
    /**
     * Switch Account
     *
     * @return void
     */
    public function testSwitchAccountEndpoint()
    {
        $user['email'] = "munnak@test.com";
        $user['password'] = '7654321';
        $token = JWTAuth::attempt($user);
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->post('/api/v1/switch-account', ['account_type' => 1]);

        $response->assertJsonFragment(['message' => 'User account switch successfully']);
    }

    /**
     * Change password
     *
     * @return void
     */
    public function testChangePassword()
    {
        $user['email'] = "munnak@test.com";
        $user['password'] = '7654321';
        $token = JWTAuth::attempt($user);
        $user['oldpassword'] = '7654321';
        $user['newpassword'] = '7654321';
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->post('/api/v1/change-password', $user);
        $response->assertJsonFragment(['message' => "new password can not be the old password!"]);
    }

    public function testDeleteAccount()
    {
        $user['email'] = "munnak@test.com";
        $user['password'] = '7654321';
        $token = JWTAuth::attempt($user);
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->get('/api/v1/delete-account');

        $response->dump();
    }
}
