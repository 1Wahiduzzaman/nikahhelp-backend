<?php namespace Tests\Repositories;

use App\Models\Nofification;
use App\Repositories\NofificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NofificationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NofificationRepository
     */
    protected $nofificationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->nofificationRepo = \App::make(NofificationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_nofification()
    {
        $nofification = Nofification::factory()->make()->toArray();

        $createdNofification = $this->nofificationRepo->create($nofification);

        $createdNofification = $createdNofification->toArray();
        $this->assertArrayHasKey('id', $createdNofification);
        $this->assertNotNull($createdNofification['id'], 'Created Nofification must have id specified');
        $this->assertNotNull(Nofification::find($createdNofification['id']), 'Nofification with given id must be in DB');
        $this->assertModelData($nofification, $createdNofification);
    }

    /**
     * @test read
     */
    public function test_read_nofification()
    {
        $nofification = Nofification::factory()->create();

        $dbNofification = $this->nofificationRepo->find($nofification->id);

        $dbNofification = $dbNofification->toArray();
        $this->assertModelData($nofification->toArray(), $dbNofification);
    }

    /**
     * @test update
     */
    public function test_update_nofification()
    {
        $nofification = Nofification::factory()->create();
        $fakeNofification = Nofification::factory()->make()->toArray();

        $updatedNofification = $this->nofificationRepo->update($fakeNofification, $nofification->id);

        $this->assertModelData($fakeNofification, $updatedNofification->toArray());
        $dbNofification = $this->nofificationRepo->find($nofification->id);
        $this->assertModelData($fakeNofification, $dbNofification->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_nofification()
    {
        $nofification = Nofification::factory()->create();

        $resp = $this->nofificationRepo->delete($nofification->id);

        $this->assertTrue($resp);
        $this->assertNull(Nofification::find($nofification->id), 'Nofification should not exist in DB');
    }
}
