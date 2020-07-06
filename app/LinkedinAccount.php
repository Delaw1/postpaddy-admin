<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkedinAccount extends Model
{
    protected $fillable = [
        'company_id', 'linkedin_access_token'
    ];

    protected $hidden = [
        'linkedin_access_token',
    ];

    public function company(){
        return $this->hasOne("App\Company", "id", "company_id");
    }
}
