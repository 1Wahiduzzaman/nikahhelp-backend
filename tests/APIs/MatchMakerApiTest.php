<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MatchMaker;

class MatchMakerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_match_maker()
    {
        $matchMaker = MatchMaker::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/match_makers', $matchMaker
        );

        $this->assertApiResponse($matchMaker);
    }

    /**
     * @test
     */
    public function test_read_match_maker()
    {
        $matchMaker = MatchMaker::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/match_makers/'.$matchMaker->id
        );

        $this->assertApiResponse($matchMaker->toArray());
    }

    /**
     * @test
     */
    public function test_update_match_maker()
    {
        $matchMaker = MatchMaker::factory()->create();
        $editedMatchMaker = MatchMaker::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/match_makers/'.$matchMaker->id,
            $editedMatchMaker
        );

        $this->assertApiResponse($editedMatchMaker);
    }

    /**
     * @test
     */
    public function test_delete_match_maker()
    {
        $matchMaker = MatchMaker::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/match_makers/'.$matchMaker->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/match_makers/'.$matchMaker->id
        );

        $this->response->assertStatus(404);
    }
}
