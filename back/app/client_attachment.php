<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class client_attachment extends Model
{
    protected $fillable = [
        'client_id', 'file_name', 'file_ext', 'type', 'description', 'date_applied'
    ];

    public const VALIDATION_RULES = [
        'client_id' => ['required'],
        'type' => ['required'],
        'date_applied' => ['required']
    ];
}
