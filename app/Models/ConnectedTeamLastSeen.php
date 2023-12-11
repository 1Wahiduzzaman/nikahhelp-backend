<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectedTeamLastSeen extends Model
{
    use HasFactory;
    protected $tables = 'connected_team_last_seens';
    protected $fillable = ['team_chat_id', 'user_id', 'last_seen_msg_id'];
}
