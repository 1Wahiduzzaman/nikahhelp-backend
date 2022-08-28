<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketSubmission extends Model
{
    use HasFactory;

    protected $table = 'submit_ticket';

    protected $fillable = [
        'issue',
        'issue_type',
        'user',
        'user_id',
        'screen_shot_path',
        'screen_shot_id'
    ];

    public function user()
    {
       return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function processTicket()
    {
        return $this->hasOne(ProcessTicket::class, 'ticket_id', 'id');
    }
}
