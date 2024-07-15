<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\block_list;

class block_listApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_block_list()
    {
        $blockList = block_list::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/block_lists', $blockList
        );

        $this->assertApiResponse($blockList);
    }

    /**
     * @test
     */
    public function test_read_block_list()
    {
        $blockList = block_list::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/block_lists/'.$blockList->id
        );

        $this->assertApiResponse($blockList->toArray());
    }

    /**
     * @test
     */
    public function test_update_block_list()
    {
        $blockList = block_list::factory()->create();
        $editedblock_list = block_list::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/block_lists/'.$blockList->id,
            $editedblock_list
        );

        $this->assertApiResponse($editedblock_list);
    }

    /**
     * @test
     */
    public function test_delete_block_list()
    {
        $blockList = block_list::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/block_lists/'.$blockList->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/block_lists/'.$blockList->id
        );

        $this->response->assertStatus(404);
    }
}
