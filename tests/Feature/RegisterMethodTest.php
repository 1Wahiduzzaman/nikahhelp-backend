<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterMethodTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testEmailRequired()
    {
        $user[''] = 8;
        $response = $this->post('/api/v1/register', $user);
        $response->assertJsonFragment(['email' => ['The email field is required.']]);
    }

    public function testEmailNeedToBeValid()
    {
        $user['email'] = '0';
        $response = $this->post('/api/v1/register', $user);

        $response->assertJsonFragment(['email' => ['The email must be a valid email address.']]);
    }

    public function testEmailIsWorking()
    {
        $user['email'] = 'test@email.com';
        $response = $this->post('/api/v1/register', $user);

        $response->assertJsonMissing(['token']);
        $response->assertJsonMissingValidationErrors('email');
    }

    public function testPasswordIsRequired()
    {
        $user['email'] = 'test@email.com';
        $user[''] = 12344;
        $response = $this->post('/api/v1/register', $user);

        $response->assertJsonFragment(['password' => ['The password field is required.']]);
    }

    public function testPasswordIsNotValid()
    {
        $user['email'] = 'test@test.com';
        $user['password'] = 123456;
        $response = $this->post('/api/v1/register', $user);
        $error = [
            "password" => [
                "The password is incorrect.",
                "The password must be a string.",
                "The password must be at least 8 characters."
            ],
        ];
        $response->assertJsonFragment($error);
    }

    public function testPasswordisValid()
    {
        $user['email'] = 'test@test.com';
        $user['password'] = "testingg1";
        $response = $this->post('/api/v1/register', $user);

        $response->assertJsonMissingValidationErrors('password');
    }


}
