<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = ['from_team_id', 'to_team_id', 'user_id', 'visit_count', 'lat', 'lng', 'country'];
}
