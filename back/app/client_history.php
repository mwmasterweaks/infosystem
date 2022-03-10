<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class client_history extends Model
{
    protected $fillable = ["client_id", "title", "details", "user_id"];
}
