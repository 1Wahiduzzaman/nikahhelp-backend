<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportChat extends Model
{
    use HasFactory;
    protected $tables = 'support_chats';
    protected $fillable = ['sender', 'receiver'];
    
    public function sender_data() {
        return $this->belongsTo(User::class, 'sender', 'id');
    }
    public function receiver_data() {
        return $this->belongsTo(User::class, 'receiver', 'id');
    }
    public function last_message() {
        return $this->hasOne(SupportMessage::class, 'chat_id', 'id')->orderBy('created_at', 'desc');
    }
    public function message_history() {
        return $this->hasMany(SupportMessage::class, 'chat_id', 'id')->orderBy('created_at', 'asc');
    }
}
