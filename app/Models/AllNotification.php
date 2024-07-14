<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllNotification extends Model
{
    use HasFactory;

    public $table = 'all_notifications';

    protected $fillable = ['sender', 'receiver', 'team_id',  'seen', 'title', 'description'];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

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
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
}
