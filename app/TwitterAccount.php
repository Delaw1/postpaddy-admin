<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterAccount extends Model
{
    protected $fillable = [
        'company_id', 'oauth_token', 'oauth_token_secret', 'twitter_id', 'name'
    ];

    protected $hidden = [
        'oauth_token', 'oauth_token_secret'
    ];

    public function company(){
        return $this->hasOne("App\Company", "id", "company_id");
    }
}
