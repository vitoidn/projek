<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpRecordHeader extends Model
{
    protected $table = 'op_record_headers';

    protected $fillable = [
        'date',
        'shift_id',
        'process_main',
        'process_2',
        'prepare_signature',
        'niks',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'process_2' => 'array',
        'niks' => 'array',
    ];

    public function shift()
    {
        return $this->belongsTo(MasterShift::class, 'shift_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bodies()
    {
        return $this->hasMany(OpRecordBody::class, 'header_id')->orderBy('id');
    }

    public function getNikListAttribute()
    {
        if (!is_array($this->niks)) return $this->niks;
        $parts = [];
        $labels = [
            'bending' => 'BND',
            'shape_check_jig' => 'SCJ',
            'drawing_inspection' => 'D&I',
            'drawing' => 'DRW',
            'inspection' => 'INSP',
        ];
        foreach ($this->niks as $key => $val) {
            if ($val && !empty($val['nik'])) {
                $label = $labels[$key] ?? $key;
                $parts[] = $label . ': ' . $val['nik'] . (!empty($val['name']) ? ' (' . $val['name'] . ')' : '');
            }
        }
        return implode(' | ', $parts);
    }

    public function getProcess2ListAttribute()
    {
        return is_array($this->process_2) ? implode(', ', $this->process_2) : $this->process_2;
    }
}
