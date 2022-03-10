<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;

class schedule extends Model
{
    protected $fillable = [
        'client_detail_id', 'ticket_id', 'team_id', 'target_date', 'type', 'status'
    ];

    public function team()
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }
}
