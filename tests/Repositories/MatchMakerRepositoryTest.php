<?php namespace Tests\Repositories;

use App\Models\MatchMaker;
use App\Repositories\MatchMakerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MatchMakerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MatchMakerRepository
     */
    protected $matchMakerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->matchMakerRepo = \App::make(MatchMakerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_match_maker()
    {
        $matchMaker = MatchMaker::factory()->make()->toArray();

        $createdMatchMaker = $this->matchMakerRepo->create($matchMaker);

        $createdMatchMaker = $createdMatchMaker->toArray();
        $this->assertArrayHasKey('id', $createdMatchMaker);
        $this->assertNotNull($createdMatchMaker['id'], 'Created MatchMaker must have id specified');
        $this->assertNotNull(MatchMaker::find($createdMatchMaker['id']), 'MatchMaker with given id must be in DB');
        $this->assertModelData($matchMaker, $createdMatchMaker);
    }

    /**
     * @test read
     */
    public function test_read_match_maker()
    {
        $matchMaker = MatchMaker::factory()->create();

        $dbMatchMaker = $this->matchMakerRepo->find($matchMaker->id);

        $dbMatchMaker = $dbMatchMaker->toArray();
        $this->assertModelData($matchMaker->toArray(), $dbMatchMaker);
    }

    /**
     * @test update
     */
    public function test_update_match_maker()
    {
        $matchMaker = MatchMaker::factory()->create();
        $fakeMatchMaker = MatchMaker::factory()->make()->toArray();

        $updatedMatchMaker = $this->matchMakerRepo->update($fakeMatchMaker, $matchMaker->id);

        $this->assertModelData($fakeMatchMaker, $updatedMatchMaker->toArray());
        $dbMatchMaker = $this->matchMakerRepo->find($matchMaker->id);
        $this->assertModelData($fakeMatchMaker, $dbMatchMaker->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_match_maker()
    {
        $matchMaker = MatchMaker::factory()->create();

        $resp = $this->matchMakerRepo->delete($matchMaker->id);

        $this->assertTrue($resp);
        $this->assertNull(MatchMaker::find($matchMaker->id), 'MatchMaker should not exist in DB');
    }
}
