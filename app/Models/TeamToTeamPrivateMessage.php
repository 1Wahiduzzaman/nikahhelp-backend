<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamToTeamPrivateMessage extends Model
{
    use HasFactory;

    public function team_chat() {
        return $this->belongsTo(TeamPrivateChat::class, 'team_private_chat_id', 'id');
    }        
}
