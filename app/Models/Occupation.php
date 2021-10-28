<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'status',
    ];
}
