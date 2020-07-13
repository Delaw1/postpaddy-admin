<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\TwitterAccount;
use \App\LinkedinAccount;

class Company extends Model
{
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
        $linkedin = LinkedinAccount::where("company_id", "=", $this->id)->count() > 0;

        if($twitter){array_push($data, "twitter");}
        if($linkedin){array_push($data, "linkedin");}
        
        return $data;
    }
}
