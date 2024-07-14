<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessTicket extends Model
{
    use HasFactory;

    protected $table = 'ticket_processing';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'status',
        'user'
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketSubmission::class, 'id', 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
