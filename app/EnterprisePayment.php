<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnterprisePayment extends Model
{
    protected $fillable = ['user_id', 'enterprise_id', 'clients', 'posts', 'remove_social', 'price'];
}
