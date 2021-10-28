<?php


namespace App\Transformers;

use App\Models\Team;
use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Class CandidateTransformer
 * @package App\Transformers
 */
class TeamTransformer extends TransformerAbstract
{

    /**
     * @param Team $item
     * @return array
     */
    public function transform(Team $item): array
    {
        return [
            'id' => $item->id,
            Team::TEAM_ID => $item->{Team::TEAM_ID},
            Team::NAME => $item->{Team::NAME},
            Team::DESCRIPTION => $item->{Team::DESCRIPTION},
            Team::MEMBER_COUNT => +$item->{Team::MEMBER_COUNT},
            Team::SUBSCRIPTION_EXPIRE_AT => $item->{Team::SUBSCRIPTION_EXPIRE_AT},
            Team::STATUS => +$item->{Team::STATUS},
            Team::CREATED_BY => $item->user->only(['id',User::FULL_NAME, User::EMAIL,User::STATUS]),
            Team::CREATED_AT => $item->{Team::CREATED_AT},
            Team::UPDATED_AT => $item->{Team::UPDATED_AT}
        ];
    }
}
