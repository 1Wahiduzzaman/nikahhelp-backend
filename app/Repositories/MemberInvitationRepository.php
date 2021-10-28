<?php

namespace App\Repositories;

use App\Models\TeamMemberInvitation;

/**
 * Class MemberInvitationRepository
 *
 * @package App\Repositories
 */
class MemberInvitationRepository  extends BaseRepository
{
    protected $modelName = TeamMemberInvitation::class;

    /**
     * MemberInvitationRepository constructor.
     *
     * @param TeamMemberInvitation $model
     */
    public function __construct(TeamMemberInvitation $model)
    {
        $this->model = $model;
    }
}
