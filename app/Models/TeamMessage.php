<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMessage extends Model
{
    use HasFactory;

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender', 'id');
    }
}
