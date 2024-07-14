<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $tables = 'chats';
    protected $fillable = ['team_id', 'sender', 'receiver'];
    
    public function sender_data() {
        return $this->belongsTo(User::class, 'sender', 'id');
    }
    public function receiver_data() {
        return $this->belongsTo(User::class, 'receiver', 'id');
    }
    public function last_message() {
        return $this->hasOne(Message::class, 'chat_id', 'id')->orderBy('created_at', 'desc');
    }
    public function message_history() {
        return $this->hasMany(Message::class, 'chat_id', 'id')->orderBy('created_at', 'asc');
    }
}
