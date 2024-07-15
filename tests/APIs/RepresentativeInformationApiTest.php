<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RepresentativeInformation;

class RepresentativeInformationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/representative_informations', $representativeInformation
        );

        $this->assertApiResponse($representativeInformation);
    }

    /**
     * @test
     */
    public function test_read_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/representative_informations/'.$representativeInformation->id
        );

        $this->assertApiResponse($representativeInformation->toArray());
    }

    /**
     * @test
     */
    public function test_update_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->create();
        $editedRepresentativeInformation = RepresentativeInformation::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/representative_informations/'.$representativeInformation->id,
            $editedRepresentativeInformation
        );

        $this->assertApiResponse($editedRepresentativeInformation);
    }

    /**
     * @test
     */
    public function test_delete_representative_information()
    {
        $representativeInformation = RepresentativeInformation::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/representative_informations/'.$representativeInformation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/representative_informations/'.$representativeInformation->id
        );

        $this->response->assertStatus(404);
    }
}
