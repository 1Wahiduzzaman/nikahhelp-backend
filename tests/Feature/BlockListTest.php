<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlockListTest extends TestCase
{
    // Block List Test - 182
    /**
     * Block List Test 
     * Need Bearer Token
     */
    public function testBlockList()
    {
        $user['email'] = "raz.doict@gmail.com";
        $user['password'] = 'aaaaaaaa';        
        $token = JWTAuth::attempt($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->get('/api/v1/block-list');             
        $response->assertStatus($response['status_code']);
    }
    /**
     * Store Block List
     * Need Bearer Token
     */
    public function testStoreBlockList()
    {
        $user['email'] = "raz.doict@gmail.com";
        $user['password'] = 'aaaaaaaa';        
        $token = JWTAuth::attempt($user);

        $data['user_id'] = 1;
        $data['block_by'] = 2;
        $data['type'] = 'single';

        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->post('/api/v1/store-block-list', $data);            
        $response->assertStatus($response['status_code']);
    }
    /**
     * Block List Test 
     * Need Bearer Token
     */
    public function testUnblockCandidate()
    {
        $user['email'] = "raz.doict@gmail.com";
        $user['password'] = 'aaaaaaaa';        
        $token = JWTAuth::attempt($user);

        $block_id = 2;
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->get('/api/v1/unblock-candidate/'.$block_id);             
        $response->assertStatus($response['status_code']);
    }
}
