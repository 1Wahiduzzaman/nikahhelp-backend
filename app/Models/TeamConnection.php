<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamConnection extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'from_team_id',
        'to_team_id',
        'requested_by',
        'responded_by',
        'connection_status',
    ];


    public function from_team()
    {
        return $this->hasOne(Team::class,'id','from_team_id');
    }

    public function to_team()
    {
        return $this->hasOne(Team::class,'id','to_team_id');
    }

    public function requested_by_user()
    {
        return $this->hasOne(User::class,'id','requested_by');
    }

    public function responded_by_user()
    {
        return $this->hasOne(User::class,'id','responded_by');
    }

    public function team_chat()
    {
        return $this->hasOne(TeamChat::class,'team_connection_id', 'id');
    }

}
