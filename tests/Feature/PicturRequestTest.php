<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PicturRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $client = new \GuzzleHttp\Client();

        $res = $client->request('POST', 'https://chobi.nikahhelp.com/api/v1/register', [
            'form_params' => [
                'email' => 'justtolet@test.com',
                'password' => 'hellowrld!23'
            ]
        ]);

        $this->assertTrue($res->getStatusCode() == 200, 'success');
        dd(json_decode($res->getBody()->getContents())->data->token->access_token);
    }
}
