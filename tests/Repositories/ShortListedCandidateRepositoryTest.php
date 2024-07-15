<?php namespace Tests\Repositories;

use App\Models\ShortListedCandidate;
use App\Repositories\ShortListedCandidateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ShortListedCandidateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ShortListedCandidateRepository
     */
    protected mixed $shortListedCandidateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->shortListedCandidateRepo = App::make(ShortListedCandidateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->make()->toArray();

        $createdShortListedCandidate = $this->shortListedCandidateRepo->create($shortListedCandidate);

        $createdShortListedCandidate = $createdShortListedCandidate->toArray();
        $this->assertArrayHasKey('id', $createdShortListedCandidate);
        $this->assertNotNull($createdShortListedCandidate['id'], 'Created ShortListedCandidate must have id specified');
        $this->assertNotNull(ShortListedCandidate::find($createdShortListedCandidate['id']), 'ShortListedCandidate with given id must be in DB');
        $this->assertModelData($shortListedCandidate, $createdShortListedCandidate);
    }

    /**
     * @test read
     */
    public function test_read_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->create();

        $dbShortListedCandidate = $this->shortListedCandidateRepo->find($shortListedCandidate->id);

        $dbShortListedCandidate = $dbShortListedCandidate->toArray();
        $this->assertModelData($shortListedCandidate->toArray(), $dbShortListedCandidate);
    }

    /**
     * @test update
     */
    public function test_update_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->create();
        $fakeShortListedCandidate = ShortListedCandidate::factory()->make()->toArray();

        $updatedShortListedCandidate = $this->shortListedCandidateRepo->update($fakeShortListedCandidate, $shortListedCandidate->id);

        $this->assertModelData($fakeShortListedCandidate, $updatedShortListedCandidate->toArray());
        $dbShortListedCandidate = $this->shortListedCandidateRepo->find($shortListedCandidate->id);
        $this->assertModelData($fakeShortListedCandidate, $dbShortListedCandidate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_short_listed_candidate()
    {
        $shortListedCandidate = ShortListedCandidate::factory()->create();

        $resp = $this->shortListedCandidateRepo->delete($shortListedCandidate->id);

        $this->assertTrue($resp);
        $this->assertNull(ShortListedCandidate::find($shortListedCandidate->id), 'ShortListedCandidate should not exist in DB');
    }
}
