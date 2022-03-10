<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Region;
use App\Engineer;

class Job_order extends Model
{
    protected $fillable = [
        'client_id', 'client_detail_id', 'region_id', 'details', 'location', 'project_description',
        'cable_category', 'foc_length', 'started', 'finished', 'engineer_in_charge', 'prepare', 'approve', 'note'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');
    }

    public function engineer()
    {
        return $this->hasOne(Engineer::class, 'id', 'engineer_in_charge');
    }

    public function note_engineer()
    {
        return $this->hasOne(Engineer::class, 'id', 'note');
    }

    public function prepare_engineer()
    {
        return $this->hasOne(Engineer::class, 'id', 'prepare');
    }

    public function approve_engineer()
    {
        return $this->hasOne(Engineer::class, 'id', 'approve');
    }
}
