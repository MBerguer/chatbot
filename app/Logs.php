<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'message'
    ];
}
