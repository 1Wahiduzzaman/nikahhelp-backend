<?php namespace Tests\Repositories;

use App\Repositories\blockListRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BlockList;

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
        $this->blockListRepo = App::make(blockListRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_block_list()
    {
        $blockList = BlockList::factory()->make()->toArray();

        $createdblock_list = $this->blockListRepo->create($blockList);

        $createdblock_list = $createdblock_list->toArray();
        $this->assertArrayHasKey('id', $createdblock_list);
        $this->assertNotNull($createdblock_list['id'], 'Created block_list must have id specified');
        $this->assertNotNull(BlockList::find($createdblock_list['id']), 'block_list with given id must be in DB');
        $this->assertModelData($blockList, $createdblock_list);
    }

    /**
     * @test read
     */
    public function test_read_block_list()
    {
        $blockList = BlockList::factory()->create();

        $dbblock_list = $this->blockListRepo->find($blockList->id);

        $dbblock_list = $dbblock_list->toArray();
        $this->assertModelData($blockList->toArray(), $dbblock_list);
    }

    /**
     * @test update
     */
    public function test_update_block_list()
    {
        $blockList = BlockList::factory()->create();
        $fakeblock_list = BlockList::factory()->make()->toArray();

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
        $blockList = BlockList::factory()->create();

        $resp = $this->blockListRepo->delete($blockList->id);

        $this->assertTrue($resp);
        $this->assertNull(BlockList::find($blockList->id), 'block_list should not exist in DB');
    }
}
