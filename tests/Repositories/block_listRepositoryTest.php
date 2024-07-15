<?php namespace Tests\Repositories;

use App\Models\block_list;
use App\Repositories\blockListRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class block_listRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var blockListRepository
     */
    protected $blockListRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->blockListRepo = \App::make(blockListRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_block_list()
    {
        $blockList = block_list::factory()->make()->toArray();

        $createdblock_list = $this->blockListRepo->create($blockList);

        $createdblock_list = $createdblock_list->toArray();
        $this->assertArrayHasKey('id', $createdblock_list);
        $this->assertNotNull($createdblock_list['id'], 'Created block_list must have id specified');
        $this->assertNotNull(block_list::find($createdblock_list['id']), 'block_list with given id must be in DB');
        $this->assertModelData($blockList, $createdblock_list);
    }

    /**
     * @test read
     */
    public function test_read_block_list()
    {
        $blockList = block_list::factory()->create();

        $dbblock_list = $this->blockListRepo->find($blockList->id);

        $dbblock_list = $dbblock_list->toArray();
        $this->assertModelData($blockList->toArray(), $dbblock_list);
    }

    /**
     * @test update
     */
    public function test_update_block_list()
    {
        $blockList = block_list::factory()->create();
        $fakeblock_list = block_list::factory()->make()->toArray();

        $updatedblock_list = $this->blockListRepo->update($fakeblock_list, $blockList->id);

        $this->assertModelData($fakeblock_list, $updatedblock_list->toArray());
        $dbblock_list = $this->blockListRepo->find($blockList->id);
        $this->assertModelData($fakeblock_list, $dbblock_list->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_block_list()
    {
        $blockList = block_list::factory()->create();

        $resp = $this->blockListRepo->delete($blockList->id);

        $this->assertTrue($resp);
        $this->assertNull(block_list::find($blockList->id), 'block_list should not exist in DB');
    }
}
