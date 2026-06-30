<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalRecordDefect extends Model
{
    public function detail() { return $this->belongsTo(OperationalRecordDetail::class, 'or_detail_id'); }
    public function defect() { return $this->belongsTo(MasterDefect::class, 'defect_id'); }
}
