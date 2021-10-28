<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Notification;

class NotificationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_notification()
    {
        $notification = Notification::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/notifications', $notification
        );

        $this->assertApiResponse($notification);
    }

    /**
     * @test
     */
    public function test_read_notification()
    {
        $notification = Notification::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/notifications/'.$notification->id
        );

        $this->assertApiResponse($notification->toArray());
    }

    /**
     * @test
     */
    public function test_update_notification()
    {
        $notification = Notification::factory()->create();
        $editedNotification = Notification::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/notifications/'.$notification->id,
            $editedNotification
        );

        $this->assertApiResponse($editedNotification);
    }

    /**
     * @test
     */
    public function test_delete_notification()
    {
        $notification = Notification::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/notifications/'.$notification->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/notifications/'.$notification->id
        );

        $this->response->assertStatus(404);
    }
}
