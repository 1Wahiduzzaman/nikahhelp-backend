<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteReason extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'team_id',
        'user_id',
        'reason_type',
        'reason_text',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }
}
