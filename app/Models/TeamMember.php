<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    const TEAM_ID = 'team_id';
    const USER_ID = 'user_id';
    const USER_TYPE = 'user_type';
    const ROLE = 'role';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT =  'updated_at';
    const RELATIONSHIP =  'relationship';

    protected $fillable = [
        self::TEAM_ID,
        self::USER_ID,
        self::USER_TYPE,
        self::ROLE,
        self::STATUS,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::RELATIONSHIP
    ];

    public function user(){
        return $this->hasOne( User::class, 'id', 'user_id');
    }

    public function getTeam(){
        return $this->hasOne( Team::class, 'id', 'team_id');
    }

    public function getCandidateInfo(){
        return $this->hasOne( CandidateInformation::class, 'user_id', 'user_id');
    }

    public function last_group_message()
    {
        return $this->hasOne(Message::class, 'sender', 'user_id')->orderBy('created_at', 'desc');
    }
    public function last_message()
    {
        return $this->hasOne(Message::class, 'receiver', 'user_id')->orderBy('created_at', 'desc');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'id', 'team_id');
    }

    public function userTeam()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
