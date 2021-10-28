<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyLevel extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "study_level";

    protected $fillable = [
        'name'
    ];
}
