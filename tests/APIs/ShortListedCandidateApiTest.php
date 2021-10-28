<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ShortListedCandidate;

class ShortListedCandidateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/short_listed_candidates', $shortListedCandidate
        );

        $this->assertApiResponse($shortListedCandidate);
    }

    /**
     * @test
     */
    public function test_read_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/short_listed_candidates/'.$shortListedCandidate->id
        );

        $this->assertApiResponse($shortListedCandidate->toArray());
    }

    /**
     * @test
     */
    public function test_update_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->create();
        $editedShortListedCandidate = ShortListedCandidate::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/short_listed_candidates/'.$shortListedCandidate->id,
            $editedShortListedCandidate
        );

        $this->assertApiResponse($editedShortListedCandidate);
    }

    /**
     * @test
     */
    public function test_delete_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/short_listed_candidates/'.$shortListedCandidate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/short_listed_candidates/'.$shortListedCandidate->id
        );

        $this->response->assertStatus(404);
    }
}
