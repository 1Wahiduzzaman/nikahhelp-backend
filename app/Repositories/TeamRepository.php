<?php

namespace App\Repositories;

use App\Models\Team;

/**
 * Class UserRepository
 */
class TeamRepository extends BaseRepository
{
    protected $modelName = Team::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(Team $model)
    {
        $this->model = $model;
    }
}
