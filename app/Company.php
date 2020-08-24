<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\TwitterAccount;
use \App\LinkedinAccount;
use \App\FacebookAccount;

class Company extends Model
{
    protected $appends = ['platformList'];

    protected $fillable = [
        'user_id', 'name', 'email_address' 
    ];

    protected $casts = [
        'platforms' => 'array' 
    ];

    public function getPlatformsAttribute($value)
    {
        $data = [];

        $twitter = TwitterAccount::where("company_id", "=", $this->id)->count() > 0;
        $facebook = FacebookAccount::where("company_id", "=", $this->id)->count() > 0;
        $linkedin = LinkedinAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->first();

        if($twitter){array_push($data, ["twitter" => []]);}
        if($facebook){array_push($data, ["facebook" => []]);}
        if($linkedin){
            array_push($data, ["linkedin" => $linkedin->accounts]);
        }
        
        return $data;
    }

    public function getPlatformListAttribute() {
        $data = [];

        $twitter = TwitterAccount::where("company_id", "=", $this->id)->count() > 0;
        $facebook = FacebookAccount::where("company_id", "=", $this->id)->count() > 0;
        $linkedin = LinkedinAccount::where("company_id", "=", $this->id)->where("accounts", "!=", '')->where("accounts", "!=", '[]')->count() > 0;

        if($twitter){array_push($data, "twitter");}
        if($facebook){array_push($data, "facebook");}
        if($linkedin){
            array_push($data, "linkedin");
        }
        
        return $data;
    }
}
