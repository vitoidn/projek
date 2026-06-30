<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MActivityCode extends Model
{
    protected $table = 'm_activity_codes';

    protected $fillable = [
        'code',
        'name',
    ];
}
