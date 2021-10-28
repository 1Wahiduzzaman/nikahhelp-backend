<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    const TEAM_ID = 'team_id';
    const NAME ='name';
    const DESCRIPTION = 'description';
    const MEMBER_COUNT = 'member_count';
    const SUBSCRIPTION_EXPIRE_AT = 'subscription_expire_at';
    const STATUS = 'status';
    const CREATED_BY = 'created_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PASSWORD = 'password';
    const LOGO = 'logo';

    protected $primaryKey = 'id';

    protected $fillable = [
        self::TEAM_ID,
        self::NAME,
        self::DESCRIPTION,
        self::MEMBER_COUNT,
        self::SUBSCRIPTION_EXPIRE_AT,
        self::STATUS,
        self::CREATED_BY,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::PASSWORD,
        self::LOGO
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::CREATED_AT => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
       return $this->hasOne(User::class,'id',self::CREATED_BY);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function created_by()
    {
        // created_by information
       return $this->hasOne(User::class,'id',self::CREATED_BY);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function team_members(){
        return $this->hasMany( TeamMember::class, 'team_id', 'id')->with("user");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function team_invited_members(){
        return $this->hasMany( TeamMemberInvitation::class, 'team_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getTeamCandidate(){
        return $this->hasOne( TeamMember::class, 'id', 'team_id')->where('user_type','=','Candidate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function TeamlistedShortListed(){
        return $this->hasMany( ShortListedCandidate::class, 'shortlisted_for', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamRequestedConnectedList(){
        return $this->hasMany( TeamConnection::class, 'from_team_id', 'id')->where('connection_status','=','1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamRequestedAcceptedConnectedList(){
        return $this->hasMany( TeamConnection::class, 'to_team_id', 'id')->where('connection_status','=','1');
    }

    public function last_group_message() {
        return $this->hasOne(TeamMessage::class, 'team_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function last_team_2_team_message() {
        return $this->hasOne(TeamToTeamMessage::class, 'from_team_id', 'id')->orderBy('created_at', 'DESC');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamConnected(){
        $RequestedConnectedList=0;
       $RequestedAcceptedConnectedList=0;
        if(empty($this->teamRequestedConnectedList()) && count($this->teamRequestedConnectedList())>0){
            $RequestedConnectedList=count($this->teamRequestedConnectedList());
        }
        if(empty($this->teamRequestedAcceptedConnectedList()) && count($this->teamRequestedAcceptedConnectedList())>0){
            $RequestedAcceptedConnectedList=count($this->teamRequestedAcceptedConnectedList());
        }
        return $RequestedConnectedList + $RequestedAcceptedConnectedList;
    }
}
