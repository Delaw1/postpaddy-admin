<?php

namespace App;

// use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use DateTime;
use App\Plan;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'status', 'category',
        'business_name', 'phone', 'employees', 'image', 'plan_id', 'started_at', 'ended_at',
        'role', 'email_verified_at'
    ];

    protected $appends = ['daysLeft', 'plan', 'updateprofile'];
    // protected $appends = ['plan'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getImageAttribute($value)
    {
        if ($value !== NULL) {
            return env("APP_CALLBACK_BASE_URL") . "/profile/" . $value;
        }
        return $value;
    }

    public function getDaysLeftAttribute()
    {
        if ($this->role == "user") {
            $start_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
            $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $this->ended_at);
            $different_days = $start_date->diffInDays($end_date);

            $now = strtotime(Carbon::now());
            $end = strtotime($this->ended_at);
            if ($end >= $now) {
                return $different_days;
            }
            if ($different_days === 0) {
                return $different_days;
            } else {
                return -1 * $different_days;
            }
            // return 0;
        }
    }

    public function getPlanAttribute()
    {
        if ($this->role == "user") {
            $plan = Plan::find($this->plan_id);
            return $plan;
        }
    }

    public function getUpdateProfileAttribute()
    {
        if ($this->role == "user") {
            return $this->created_at->addDays(5);
        }
        
    }

    public function AauthAcessToken()
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function Plan()
    {
        return $this->belongsTo('App\Plan');
    }
}
