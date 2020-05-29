<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Servers extends Model
{
    protected $table = 'servers';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'ip', 'user', 'port', 'password'
    ];

}
