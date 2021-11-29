<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMemberInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'email',
        'role',
        'password',
        'is_read',
        'created_at',
        'updated_at',
        'user_type',
        'relationship',
        'link'
    ];

    public function team() {
        return $this->BelongsTo(Team::class, 'team_id', 'id');
    }
}
