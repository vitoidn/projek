<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horenzo extends Model
{
    public function line() { return $this->belongsTo(MasterLine::class, 'line_id'); }
    public function shift() { return $this->belongsTo(MasterShift::class, 'shift_id'); }
}
