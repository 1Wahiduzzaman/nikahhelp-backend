<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Generic extends Model
{
    use HasFactory;

    public function getActiveTeamId()
    {
        $user_id = Auth::id();
        $active_team = TeamMember::where('user_id', $user_id)
            ->where('status', 1)
            ->first();
        $active_team_id = isset($active_team) ? $active_team->team_id : 0;

        return $active_team_id;
    }
}
