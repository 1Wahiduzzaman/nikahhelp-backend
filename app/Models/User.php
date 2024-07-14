<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Billable, HasFactory, Notifiable;

    const FULL_NAME = 'full_name';

    const EMAIL = 'email';

    const EMAIL_VERIFIED_AT = 'email_verified_at';

    const IS_VERIFIED = 'is_verified';

    const PASSWORD = 'password';

    const STATUS = 'status';

    const LOCKED_AT = 'locked_at';

    const LOCKED_END = 'locked_end';

    const REMEMBER_TOKEN = 'remember_token';

    const ACCOUNT_TYPE = 'account_type';

    const FORM_TYPE = 'form_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FULL_NAME,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::IS_VERIFIED,
        self::PASSWORD,
        self::STATUS,
        self::LOCKED_AT,
        self::LOCKED_END,
        self::ACCOUNT_TYPE,
        self::FORM_TYPE,
        'stripe_id',
        'two_factor_code',
        'two_factor_expires_at',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trial_ends_at',
        'login_count',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::EMAIL_VERIFIED_AT => 'datetime',
    ];

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verifyUser()
    {
        return $this->hasOne('App\Models\VerifyUser');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getCandidate()
    {
        return $this->hasOne(CandidateInformation::class, 'user_id', 'id');
    }

    public function candidate_info()
    {
        return $this->hasOne(CandidateInformation::class, 'user_id', 'id');
    }

    public function representative_info()
    {
        return $this->hasOne(RepresentativeInformation::class, 'user_id', 'id');
    }

    public function candidate_image()
    {
        return $this->hasMany(CandidateImage::class, 'user_id', 'id');
    }

    public function rejected_notes()
    {
        return $this->hasMany(RejectedNote::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getRepresentative()
    {
        return $this->hasOne(RepresentativeInformation::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getMatchmaker()
    {
        return $this->hasOne(MatchMaker::class, 'user_id', 'id');
    }

    public function last_message()
    {
        return $this->hasOne(Message::class, 'receiver', 'id')->orderBy('created_at', 'desc');
    }

    public function block_list()
    {
        return $this->hasOne(BlockList::class, 'receiver', 'id')->orderBy('created_at', 'desc');
    }

    public function ticketSubmission()
    {
        return $this->hasMany(TicketSubmission::class, 'user_id', 'id');
    }

    public function processTicket()
    {
        return $this->hasMany(ProcessTicket::class, 'user_id', 'id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'created_by', 'id');
    }

    public function team_member()
    {
        return $this->hasOne(TeamMember::class, 'user_id', 'id');
    }

    /**
     * Generate 6 digits MFA code for the User
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet

        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    /**
     * Reset the MFA code generated earlier
     */
    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    /**
     * Increment the login count
     */
    public function incrementLoginCount()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet
        $this->login_count++;
        $this->save();
    }

    /**
     * Reset the login count
     */
    public function resetLoginCount()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet
        $this->login_count = 0;
        $this->save();
    }
}
