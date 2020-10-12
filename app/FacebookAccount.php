<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{
    protected $fillable = [
        'company_id', 'access_token', 'facebook_id', 'accounts'
    ];

    protected $hidden = [
        'linkedin_access_token',
    ];

    public function company(){
        return $this->hasOne("App\Company", "id", "company_id");
    }
}
