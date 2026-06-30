<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpRecordBody extends Model
{
    protected $table = 'op_record_bodies';

    protected $fillable = [
        'header_id',
        'part_code',
        'lot_id',
        'code_id',
        'start_time',
        'end_time',
        'duration_min',
        'qty',
        'ng',
        'hold',
        'remark',
    ];

    public function header()
    {
        return $this->belongsTo(OpRecordHeader::class, 'header_id');
    }

    public function lot()
    {
        return $this->belongsTo(MLotNumber::class, 'lot_id');
    }

    public function code()
    {
        return $this->belongsTo(MActivityCode::class, 'code_id');
    }
}
