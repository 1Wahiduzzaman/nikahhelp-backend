<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PictureServerToken extends Model
{
    use HasFactory;

    protected $table = 'picture_server_token';

    protected $fillable = [
        'user_id',
        'token',
        'user_uuid'
    ];
}
