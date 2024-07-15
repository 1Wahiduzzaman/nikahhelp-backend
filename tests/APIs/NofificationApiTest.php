<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Notification;

class NofificationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_nofification()
    {
        $nofification = Notification::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/nofifications', $nofification
        );

        $this->assertApiResponse($nofification);
    }

    /**
     * @test
     */
    public function test_read_nofification()
    {
        $nofification = Notification::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/nofifications/'.$nofification->id
        );

        $this->assertApiResponse($nofification->toArray());
    }

    /**
     * @test
     */
    public function test_update_nofification()
    {
        $nofification = Notification::factory()->create();
        $editedNofification = Notification::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/nofifications/'.$nofification->id,
            $editedNofification
        );

        $this->assertApiResponse($editedNofification);
    }

    /**
     * @test
     */
    public function test_delete_nofification()
    {
        $nofification = Notification::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/nofifications/'.$nofification->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/nofifications/'.$nofification->id
        );

        $this->response->assertStatus(404);
    }
}
