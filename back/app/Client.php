<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ticket_remarks_log;
use App\ticket_comments_log;
use App\client_status_history;
use App\Package;
use App\Modem;
use App\Region;
use App\Package_type;
use App\Client_detail;
use App\Sales;
use App\bucket;
use App\sales_agent;
use App\Engineer;
use App\pon;
use App\splitter_port;
use App\Branch;
use App\area;
use App\billing;
use App\User;

class Client extends Model
{
    protected $fillable = [
        'id', 'account_no', 'acc_no', 'region_id', 'area_id', 'branch_id', 'name', 'owner_name', 'location', 'contact_person', 'business_type',
        'contact', 'email_add', 'contract', 'remarks', 'term', 'OTC', 'sales_id', 'sales_agent_id',
        'engineers_id', 'package_id', 'modem_id', 'communication_protocol', 'package_type_id',
        'pon_id', 'onu', 'modem_mac_address', 'vlan', 'ip_assigned', 'date_assigned',
        'date_activated', 'wfs', 'lat', 'lng', 'bucket_id', 'subscription_name', 'status'
    ];

    public function package()
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }

    public function modem()
    {
        return $this->hasOne(Modem::class, 'id', 'modem_id');
    }

    public function package_type()
    {
        return $this->hasOne(Package_type::class, 'id', 'package_type_id');
    }

    public function region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function area()
    {
        return $this->hasOne(area::class, 'id', 'area_id');
    }

    public function sales()
    {
        return $this->hasOne(Sales::class, 'id', 'sales_id');
    }

    public function sales_agent()
    {
        return $this->hasOne(sales_agent::class, 'id', 'sales_agent_id');
    }

    public function bucket()
    {
        return $this->hasOne(bucket::class, 'id', 'bucket_id');
    }

    public function engineer()
    {
        return $this->hasOne(Engineer::class, 'id', 'engineers_id');
    }

    public function pon()
    {
        return $this->hasOne(pon::class, 'id', 'pon_id');
    }

    public function client_detail()
    {
        return $this->belongsTo(Client_detail::class, 'client_id', 'id');
    }

    public function clientDetail()
    {
        return $this->hasOne(Client_detail::class, 'client_id', 'id');
    }

    public function splitter_port()
    {
        return $this->belongsTo(splitter_port::class, 'id', 'going_id')
            ->where('going', 'Client');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function remarks_log()
    {
        return $this->hasMany(ticket_remarks_log::class, 'ticket_id', 'id')->where("form_type", 'client')->latest();
    }

    public function comments_log()
    {
        return $this->hasMany(ticket_comments_log::class, 'ticket_id', 'id')->where("form_type", 'client')->latest();
    }


    public function status_log()
    {
        return $this->hasMany(client_status_history::class, 'client_id', 'id')->latest();
    }

    public function billings()
    {
        return $this->hasMany(billing::class, 'client_id', 'id')->latest();
    }
}
