<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLotNumber extends Model
{
    protected $table = 'm_lot_numbers';

    protected $fillable = [
        'code',
        'year',
        'month',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
