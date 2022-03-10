<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\schedule;
use App\Job_order;
use App\installation_remarks_log;

class Client_detail extends Model
{
    protected $fillable = [
        'client_id', 'otc', 'contract_status', 'mapping_status', 'cable_category', 'foc_length',
        'foc_layout', 'foc_schedule', 'foc_plan_duration', 'layout_remarks', 'modem_status',
        'applied_date', 'inst_remarks', 'target_date', 'date_activated', 'aging', 'status',
        'line_transfer', 'team_assigned', 'layout_status'
    ];

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function JobOrder()
    {
        return $this->hasOne(Job_order::class, 'client_detail_id', 'id')
            ->where('project_description', 'Installation');
    }

    public function schedule()
    {
        return $this->hasOne(schedule::class, 'client_detail_id', 'id')
            ->where('client_details.target_date', '=', 'schedules.target_date');
    }


    public function remarks_log()
    {
        return $this->hasMany(installation_remarks_log::class, 'client_detail_id', 'id')->latest();
    }
}
