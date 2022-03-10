<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\bill_state_list;
use App\Client;

class bill_statement extends Model
{
    protected $fillable = [
        'id', 'client_id', 'date', 'due_date', 'amount_due'
    ];

    public function data()
    {
        return $this->hasMany(bill_state_list::class, 'bill_statement_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
