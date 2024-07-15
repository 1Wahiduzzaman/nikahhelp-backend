<?php

namespace App\Repositories;

use App\Models\TeamMemberInvitation;

/**
 * Class MemberInvitationRepository
 */
class MemberInvitationRepository extends BaseRepository
{
    protected $modelName = TeamMemberInvitation::class;

    /**
     * MemberInvitationRepository constructor.
     */
    public function __construct(TeamMemberInvitation $model)
    {
        $this->model = $model;
    }
}
