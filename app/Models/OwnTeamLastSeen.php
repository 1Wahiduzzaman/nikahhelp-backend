<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnTeamLastSeen extends Model
{
    use HasFactory;
    protected $tables = 'own_team_last_seens';
    protected $fillable = ['team_id', 'user_id', 'last_seen_msg_id'];
}
