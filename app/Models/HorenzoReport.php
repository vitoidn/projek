<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorenzoReport extends Model
{
    protected $table = 'horenzo_reports';

    protected $fillable = [
        'filter_params',
        'generated_by',
        'snapshot_data',
    ];

    protected $casts = [
        'filter_params' => 'array',
        'snapshot_data' => 'array',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
