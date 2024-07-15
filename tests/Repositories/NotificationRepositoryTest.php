<?php namespace Tests\Repositories;

use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NotificationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationRepository
     */
    protected $notificationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->notificationRepo = \App::make(NotificationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_notification()
    {
        $notification = Notification::factory()->make()->toArray();

        $createdNotification = $this->notificationRepo->create($notification);

        $createdNotification = $createdNotification->toArray();
        $this->assertArrayHasKey('id', $createdNotification);
        $this->assertNotNull($createdNotification['id'], 'Created Notification must have id specified');
        $this->assertNotNull(Notification::find($createdNotification['id']), 'Notification with given id must be in DB');
        $this->assertModelData($notification, $createdNotification);
    }

    /**
     * @test read
     */
    public function test_read_notification()
    {
        $notification = Notification::factory()->create();

        $dbNotification = $this->notificationRepo->find($notification->id);

        $dbNotification = $dbNotification->toArray();
        $this->assertModelData($notification->toArray(), $dbNotification);
    }

    /**
     * @test update
     */
    public function test_update_notification()
    {
        $notification = Notification::factory()->create();
        $fakeNotification = Notification::factory()->make()->toArray();

        $updatedNotification = $this->notificationRepo->update($fakeNotification, $notification->id);

        $this->assertModelData($fakeNotification, $updatedNotification->toArray());
        $dbNotification = $this->notificationRepo->find($notification->id);
        $this->assertModelData($fakeNotification, $dbNotification->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_notification()
    {
        $notification = Notification::factory()->create();

        $resp = $this->notificationRepo->delete($notification->id);

        $this->assertTrue($resp);
        $this->assertNull(Notification::find($notification->id), 'Notification should not exist in DB');
    }
}
