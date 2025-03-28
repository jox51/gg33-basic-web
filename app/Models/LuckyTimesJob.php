<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuckyTimesJob extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'status',
        'name',
        'result',
        'error'
    ];

    protected $casts = [
        'result' => 'array'
    ];
}
