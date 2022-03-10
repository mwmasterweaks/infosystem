<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ticket_attachment extends Model
{
    protected $fillable = [
        'ticket_id', 'filename'
    ];

    public function file()
    {
        return $this->hasOne(Ticket::class, 'id', 'ticket_id');
    }
}
