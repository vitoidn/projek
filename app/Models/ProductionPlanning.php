<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionPlanning extends Model
{
    public function line() { return $this->belongsTo(MasterLine::class, 'line_id'); }
    public function shift() { return $this->belongsTo(MasterShift::class, 'shift_id'); }
    public function part() { return $this->belongsTo(MasterPart::class, 'part_id'); }
}
