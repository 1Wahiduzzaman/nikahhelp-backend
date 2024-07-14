<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = ['sender', 'receiver', 'team_id', 'seen', 'body', 'attachment'];

    public function team() {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sender', 'id');
    }
    public function receiver() {
        return $this->belongsTo(User::class, 'receiver', 'id');
    }

    public function chat() {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }

    // public function team_member() {
    //     $this->return->belongsTo(TeamMember::class, 'sender', 'id');
    // }
}
