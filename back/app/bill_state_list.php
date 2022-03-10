<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bill_state_list extends Model
{
    protected $fillable = [
        'id', 'bill_statement_id', 'date', 'description', 'priceFormated', 'balanceFormated'
    ];
}
