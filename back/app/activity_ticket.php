<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\activity_ticket_type;
use App\ticket_remarks_log;
use App\ticket_comments_log;
use App\Client;
use App\User;

class activity_ticket extends Model
{
    protected $fillable = [
        'ticket_type_id', 'client_id', 'created_by', 'updated_by', 'status', 'state'
    ];

    public function ticket_type()
    {
        return $this->hasOne(activity_ticket_type::class, 'id', 'ticket_type_id');
    }


    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }


    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updated_by()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function remarks_log()
    {
        return $this->hasMany(ticket_remarks_log::class, 'ticket_id', 'id')->where("form_type", 'jobOrder')->latest();
    }
    public function comments_log()
    {
        return $this->hasMany(ticket_comments_log::class, 'ticket_id', 'id')->where("form_type", 'jobOrder')->latest();
    }
}
