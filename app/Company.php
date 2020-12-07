<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\TwitterAccount;
use \App\LinkedinAccount;
use \App\FacebookAccount;

class Company extends Model
{
    protected $appends = ['platformList', 'postLeft'];

    protected $fillable = [
        'user_id', 'name', 'email_address', 'category', 'image'
    ];

    protected $casts = [
        'platforms' => 'array',
        'removed' => 'array'
    ];

    public function getPlatformsAttribute($value)
    {
        $data = [];

        $twitter = TwitterAccount::where("company_id", "=", $this->id)->first();
        $facebook = FacebookAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->first();
        $linkedin = LinkedinAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->first();

        if($twitter){array_push($data, ["twitter" => ["name" => $twitter->name, "username" => $twitter->username]]);}
        if($facebook){array_push($data, ["facebook" => $facebook->accounts]);}
        if($linkedin){
            array_push($data, ["linkedin" => $linkedin->accounts]);
        }
        
        return $data;
    }

    public function getImageAttribute($value)
    {
        if($value !== NULL) {
            return env("APP_CALLBACK_BASE_URL"). "/profile/".$value;
        }
        return $value;
    }

    public function getPostLeftAttribute()
    {
        // return $this->user;
        if($this->user->plan->name == 'Plus') {
            return $this->user->plan->posts - $this->posts;
        }
        return null;
    }

    public function getPlatformListAttribute() {
        $data = [];

        $twitter = TwitterAccount::where("company_id", "=", $this->id)->count() > 0;
        $facebook = FacebookAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->count() > 0;
        $linkedin = LinkedinAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->count() > 0;

        if($twitter){array_push($data, "twitter");}
        if($facebook){array_push($data, "facebook");}
        if($linkedin){
            array_push($data, "linkedin");
        }
        
        return $data;
    }

    public function User() {
        return $this->belongsTo('App\User');
    }
}
