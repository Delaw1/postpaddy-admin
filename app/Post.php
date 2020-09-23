<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{   
    protected $appends = ['platformList'];

    protected $fillable = [
        'user_id', 'company_id', 'content', 'media', 'platforms', 'schedule_date', 'is_posted', 'hashtag' 
    ];

    protected $casts = [
        'media' => 'array',
        'platforms' => 'array'
    ];

    public function getPlatformListAttribute() {
        return array_keys($this->platforms);
    }
}
