<?php

namespace App\Repositories;

use App\Models\Country;

/**
 * Class UserRepository
 */
class CountryRepository extends BaseRepository
{
    protected $modelName = Country::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
    }
}
