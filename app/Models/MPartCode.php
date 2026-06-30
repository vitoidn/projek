<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPartCode extends Model
{
    protected $table = 'm_part_codes';

    protected $fillable = [
        'code',
        'name',
    ];
}
