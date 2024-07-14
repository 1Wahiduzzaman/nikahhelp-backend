<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamChat extends Model
{
    use HasFactory;
    protected $tables = 'team_chats';
    protected $fillable = ['from_team_id', 'to_team_id', 'team_connection_id'];
    
    public function private_receiver_data() {
        return $this->belongsTo(User::class, 'receiver', 'id');
    }
    
    public function team_connection() {
        return $this->belongsTo(TeamConnection::class, 'team_connection_id', 'id');
    }

    public function from_team() {
        return $this->belongsTo(Team::class, 'from_team_id', 'id');
    }
    public function to_team() {
        return $this->belongsTo(Team::class, 'to_team_id', 'id');
    }
    public function last_message() {
        return $this->hasOne(TeamToTeamMessage::class, 'team_chat_id', 'id')->orderBy('created_at', 'desc');
    }    
}
