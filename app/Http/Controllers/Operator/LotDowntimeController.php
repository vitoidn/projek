<?php
namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationalRecordDetail;
use App\Models\OperationalRecordDowntime;
use Carbon\Carbon;

class LotDowntimeController extends Controller
{
    public function start(Request $request, $id)
    {
        $lot = OperationalRecordDetail::findOrFail($id);
        
        $request->validate([
            'downtime_id' => 'required|exists:master_downtimes,id'
        ]);

        if ($lot->status !== 'Running') {
            return response()->json(['error' => 'Can only start downtime when lot is Running.'], 400);
        }

        // Create downtime record
        $now = Carbon::now();
        $downtime = OperationalRecordDowntime::create([
            'or_detail_id' => $lot->id,
            'downtime_id' => $request->downtime_id,
            'start_time' => $now
        ]);

        // Update lot status to paused
        $lot->update(['status' => 'Paused']);

        return response()->json(['success' => true, 'downtime' => $downtime]);
    }

    public function end(Request $request, $id)
    {
        $lot = OperationalRecordDetail::findOrFail($id);
        
        if ($lot->status !== 'Paused') {
            return response()->json(['error' => 'No active downtime found.'], 400);
        }

        // Find the active downtime (where end_time is null)
        $downtime = OperationalRecordDowntime::where('or_detail_id', $lot->id)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        if (!$downtime) {
            return response()->json(['error' => 'Active downtime record not found.'], 404);
        }

        $now = Carbon::now();
        $start = Carbon::parse($downtime->start_time);
        $duration = $start->diffInSeconds($now);

        $downtime->update([
            'end_time' => $now,
            'duration_sec' => $duration
        ]);

        // Accumulate total downtime on the lot
        $lot->increment('total_downtime_sec', $duration);
        
        // Push estimated end forward by the downtime duration
        $newEstimated = Carbon::parse($lot->estimated_end)->addSeconds($duration);
        $lot->update([
            'status' => 'Running',
            'estimated_end' => $newEstimated
        ]);

        return response()->json(['success' => true, 'duration' => $duration, 'new_estimated_end' => $newEstimated]);
    }
}
