<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalRecordDetail extends Model
{
    public function record() { return $this->belongsTo(OperationalRecord::class, 'or_id'); }
    public function part() { return $this->belongsTo(MasterPart::class, 'part_id'); }
    public function downtimes() { return $this->hasMany(OperationalRecordDowntime::class, 'or_detail_id'); }
    public function defects() { return $this->hasMany(OperationalRecordDefect::class, 'or_detail_id'); }
}
