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
        // $data = [];

        // $twitter = TwitterAccount::where("company_id", "=", $this->id)->count() > 0;
        // $facebook = FacebookAccount::where("company_id", "=", $this->id)->count() > 0;
        // $linkedin = LinkedinAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->count() > 0;

        // if($twitter){array_push($data, "twitter");}
        // if($facebook){array_push($data, "facebook");}
        // if($linkedin){
        //     array_push($data, "linkedin");
        // }
        
        // return $data;
    }
}
