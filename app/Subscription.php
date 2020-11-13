<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'plan_id', 'clients', 'posts', 'accounts', 'remove_social', 'started_at', 'ended_at', 'enterprise_id'];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function Enterprise() {
        return $this->belongsTo('App\Enterprise');
    }
}
