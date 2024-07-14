<?php

namespace App\Repositories;

use App\Models\TeamMember;

/**
 * Class UserRepository
 */
class TeamMemberRepository extends BaseRepository
{
    protected $modelName = TeamMember::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(TeamMember $model)
    {
        $this->model = $model;
    }
}
