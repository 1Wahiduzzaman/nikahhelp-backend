<?php

namespace App\Repositories;

use App\Models\TeamConnection;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class TeamConnectionRepository  extends BaseRepository
{
    protected $modelName = TeamConnection::class;

    /**
     * UserRepository constructor.
     *
     * @param TeamConnection $model
     */
    public function __construct(TeamConnection $model)
    {
        $this->model = $model;
    }



}
