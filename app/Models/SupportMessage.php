<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $table = 'support_messages';

    protected $fillable = ['sender', 'receiver', 'type', 'seen', 'body', 'attachment'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver', 'id');
    }

    public function chat()
    {
        return $this->belongsTo(SupportChat::class, 'chat_id', 'id');
    }
}
