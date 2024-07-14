<?php

namespace App\Repositories;

use App\Models\TeamConnection;

/**
 * Class UserRepository
 */
class TeamConnectionRepository extends BaseRepository
{
    protected $modelName = TeamConnection::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(TeamConnection $model)
    {
        $this->model = $model;
    }
}
