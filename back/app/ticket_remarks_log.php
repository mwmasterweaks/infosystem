<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\ticket_comments_log;

class ticket_remarks_log extends Model
{

    protected $fillable = [
        'ticket_id', 'user_id',  'remarks', 'form_type'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(ticket_comments_log::class, 'remarks_id', 'id');
    }
}
