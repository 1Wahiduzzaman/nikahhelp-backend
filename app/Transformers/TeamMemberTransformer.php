<?php

namespace App\Transformers;

use App\Models\TeamMember;
use League\Fractal\TransformerAbstract;

/**
 * Class CandidateTransformer
 */
class TeamMemberTransformer extends TransformerAbstract
{
    public function transform(TeamMember $item): array
    {
        return [
            'id' => $item->id,
            TeamMember::TEAM_ID => +$item->{TeamMember::TEAM_ID},
            TeamMember::USER_ID => +$item->{TeamMember::USER_ID},
            TeamMember::USER_TYPE => $item->{TeamMember::USER_TYPE},
            TeamMember::ROLE => $item->{TeamMember::ROLE},
            TeamMember::STATUS => +$item->{TeamMember::STATUS},
            TeamMember::CREATED_AT => $item->{TeamMember::CREATED_AT},
            TeamMember::UPDATED_AT => $item->{TeamMember::UPDATED_AT},
        ];
    }
}
