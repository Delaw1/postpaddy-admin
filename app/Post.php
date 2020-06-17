<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{   
    protected $fillable = [
        'user_id', 'company_id', 'content', 'media', 'platforms', 'schedule_date'
    ];

    protected $casts = [
        'media' => 'array',
        'platforms' => 'array'
    ];
}
