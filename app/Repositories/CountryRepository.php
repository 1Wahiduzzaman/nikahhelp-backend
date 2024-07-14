<?php

namespace App\Repositories;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class CountryRepository  extends BaseRepository
{
    protected $modelName = Country::class;

    /**
     * UserRepository constructor.
     *
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
    }



}
