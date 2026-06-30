<?php

namespace App\Observers;

use App\Models\OperationalRecordDetail;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class OperationalRecordDetailObserver
{
    public function updated(OperationalRecordDetail $detail): void
    {
        $user = Auth::user();
        if ($user && $user->hasRole('admin')) {
            $changes = $detail->getDirty();
            $original = $detail->getOriginal();
            
            unset($changes['updated_at']);
            
            if (count($changes) > 0) {
                $oldValues = [];
                foreach ($changes as $key => $value) {
                    $oldValues[$key] = $original[$key] ?? null;
                }

                AuditLog::create([
                    'table_name' => 'operational_record_details',
                    'record_id' => $detail->id,
                    'old_value' => json_encode($oldValues),
                    'new_value' => json_encode($changes),
                    'changed_by' => $user->name . ' (' . $user->email . ')',
                    'reason' => request('reason', 'Manual Edit by Admin')
                ]);
            }
        }
    }
}
