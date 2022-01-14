<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamListedCandidate extends Model
{
    use HasFactory;

    public $fillable = [
        "id",
        "user_id",
        "team_listed_by",
        "team_listed_for",
        "team_listed_date"
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        "user_id" => 'required',
        "team_listed_by" => 'required'
    ];
}
