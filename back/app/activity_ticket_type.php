<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class activity_ticket_type extends Model
{
    protected $fillable = [
        'name', 'description'
    ];
}
