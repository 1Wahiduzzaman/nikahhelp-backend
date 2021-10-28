<?php

namespace App\Repositories;

use App\Models\Team;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class TeamRepository  extends BaseRepository
{
    protected $modelName = Team::class;

    /**
     * UserRepository constructor.
     *
     * @param Team $model
     */
    public function __construct(Team $model)
    {
        $this->model = $model;
    }



}
