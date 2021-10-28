<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\TeamMember;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class TeamMemberRepository  extends BaseRepository
{
    protected $modelName = TeamMember::class;

    /**
     * UserRepository constructor.
     *
     * @param TeamMember $model
     */
    public function __construct(TeamMember $model)
    {
        $this->model = $model;
    }



}
