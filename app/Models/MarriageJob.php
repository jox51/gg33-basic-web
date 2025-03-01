<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarriageJob extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'status',
        'names',
        'result',
        'error'
    ];

    protected $casts = [
        'result' => 'array'
    ];
}
