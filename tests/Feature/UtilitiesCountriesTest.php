<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Countries List endpoint tests
 * @author Khorram khorramk.kbsk@gmail.com
 * @copyright 2021 Matrimony
 */
class UtilitiesCountriesTest extends TestCase
{
    /**
     * Countries List endpoint
     *
     * @return void
     */
    public function testCountriesEndpoint()
    {
        $response = $this->get('/api/v1/utilities/countries');

        $response->assertJsonFragment(['message' => 'Information fetched Successfully!']);
    }
}
