<?php

namespace Tests\Feature;

use Tests\TestCase;
use JWTAuth;

/**
 * Religion endpoint testing
 * @author Khorram khorramk.kbsk@gmail.com
 * @copyright 2021 Matrimony
 */
class ReligionTest extends TestCase
{
    /**
     * Religion endpoint test
     *
     * @return void
     */
    public function testReligion()
    {
        $user['email'] = "munnak@test.com";
        $user['password'] = '7654321';
        $token = JWTAuth::attempt($user);
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->get('/api/v1/religions');

        $response->assertJsonFragment(['message' => 'Successfully retrieved']);
    }
}
