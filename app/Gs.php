<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gs extends Model
{
    protected $table = "gs";

    protected $fillable = ['remove_social_media', 'clients', 'days'];
}
