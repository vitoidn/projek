<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalRecordDowntime extends Model
{
    public function detail() { return $this->belongsTo(OperationalRecordDetail::class, 'or_detail_id'); }
    public function downtime() { return $this->belongsTo(MasterDowntime::class, 'downtime_id'); }
}
