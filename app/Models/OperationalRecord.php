<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalRecord extends Model
{
    public function line() { return $this->belongsTo(MasterLine::class, 'line_id'); }
    public function shift() { return $this->belongsTo(MasterShift::class, 'shift_id'); }
    public function details() { return $this->hasMany(OperationalRecordDetail::class, 'or_id'); }
}
