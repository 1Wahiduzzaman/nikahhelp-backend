<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamToTeamMessage extends Model
{
    use HasFactory;

    protected $table = 'team_to_team_messages';

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender', 'id');
    }

    public function from_team()
    {
        return $this->hasOne(Team::class, 'id', 'from_team_id');
    }

    public function to_team()
    {
        return $this->hasOne(Team::class, 'id', 'to_team_id');
    }
}
