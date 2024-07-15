<?php namespace Tests\Repositories;

use App\Models\RepresentativeInformation;
use App\Repositories\RepresentativeInformationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RepresentativeInformationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RepresentativeInformationRepository
     */
    protected $representativeInformationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->representativeInformationRepo = App::make(RepresentativeInformationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->make()->toArray();

        $createdRepresentativeInformation = $this->representativeInformationRepo->create($representativeInformation);

        $createdRepresentativeInformation = $createdRepresentativeInformation->toArray();
        $this->assertArrayHasKey('id', $createdRepresentativeInformation);
        $this->assertNotNull($createdRepresentativeInformation['id'], 'Created RepresentativeInformation must have id specified');
        $this->assertNotNull(RepresentativeInformation::find($createdRepresentativeInformation['id']), 'RepresentativeInformation with given id must be in DB');
        $this->assertModelData($representativeInformation, $createdRepresentativeInformation);
    }

    /**
     * @test read
     */
    public function test_read_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->create();

        $dbRepresentativeInformation = $this->representativeInformationRepo->find($representativeInformation->id);

        $dbRepresentativeInformation = $dbRepresentativeInformation->toArray();
        $this->assertModelData($representativeInformation->toArray(), $dbRepresentativeInformation);
    }

    /**
     * @test update
     */
    public function test_update_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->create();
        $fakeRepresentativeInformation = RepresentativeInformation::factory()->make()->toArray();

        $updatedRepresentativeInformation = $this->representativeInformationRepo->update($fakeRepresentativeInformation, $representativeInformation->id);

        $this->assertModelData($fakeRepresentativeInformation, $updatedRepresentativeInformation->toArray());
        $dbRepresentativeInformation = $this->representativeInformationRepo->find($representativeInformation->id);
        $this->assertModelData($fakeRepresentativeInformation, $dbRepresentativeInformation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->create();

        $resp = $this->representativeInformationRepo->delete($representativeInformation->id);

        $this->assertTrue($resp);
        $this->assertNull(RepresentativeInformation::find($representativeInformation->id), 'RepresentativeInformation should not exist in DB');
    }
}
