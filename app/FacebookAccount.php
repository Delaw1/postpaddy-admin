<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{
    protected $fillable = [
        'company_id', 'oauth_token', 'facebook_id', 'oauth_token_secret'
    ];

    protected $hidden = [
        'linkedin_access_token',
    ];

    public function company(){
        return $this->hasOne("App\Company", "id", "company_id");
    }
}
