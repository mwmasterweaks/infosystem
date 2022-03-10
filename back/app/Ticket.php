<?php

namespace App;

use App\Ticket_status;
use App\ticket_remarks_log;
use App\ticket_comments_log;
use App\Client;
use App\User;
use App\area;
use App\tickets_trouble_type;
use App\complaint_list;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'client_id', 'user_id', 'updated_by', 'ticket_id', 'complain', 'findings', 'action', 'complaint_new',
        'bwmon', 'device', 'loss', 'downtime', 'cacti', 'rep_findings', 'rep_action', 'rebatable', 'remarks',
        'report', 'prev_status', 'Status_Ticket_id', 'area_id', 'technical_assigned', 'target_date', 'team_assigned',
        'downtime_hours', 'date_time_fixed', 'to_soa',
    ];

    public function Ticket_status()
    {
        return $this->hasOne(Ticket_status::class, 'id', 'Status_Ticket_id');
    }

    public function area()
    {
        return $this->hasOne(area::class, 'id', 'area_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function complaint()
    {
        return $this->hasOne(complaint_list::class, 'id', 'complaint_new');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function remarks_log()
    {
        return $this->hasMany(ticket_remarks_log::class, 'ticket_id', 'id')->where("form_type", 'ticket')->latest();
    }

    public function comments_log()
    {
        return $this->hasMany(ticket_comments_log::class, 'ticket_id', 'id')->where("form_type", 'ticket')->latest();
    }


    public function trouble_type()
    {
        return $this->hasMany(tickets_trouble_type::class, 'ticket_id', 'id');
    }

    public function ticket_attachment()
    {
        return $this->hasMany(ticket_attachment::class, 'ticket_id', 'id');
    }
}
