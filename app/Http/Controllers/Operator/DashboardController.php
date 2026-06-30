<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use App\Models\MasterShift;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        $todayRecords = OpRecordHeader::where('created_by', $userId)
            ->whereDate('date', $today)
            ->count();

        $draftRecords = OpRecordHeader::where('created_by', $userId)
            ->where('status', 'draft')
            ->count();

        $todayBodyStats = OpRecordBody::whereHas('header', function ($q) use ($userId, $today) {
                $q->where('created_by', $userId)->whereDate('date', $today);
            })
            ->selectRaw('COUNT(*) as total_rows, COALESCE(SUM(duration_min), 0) as total_min, COALESCE(SUM(qty), 0) as total_qty')
            ->first();

        $myRecords = OpRecordHeader::with(['shift', 'bodies'])
            ->where('created_by', $userId)
            ->latest()
            ->take(10)
            ->get();

        $shifts = MasterShift::orderBy('name')->get();
        $processMains = ['Manual Bending', 'Auto Bending'];
        $process2Options = ['Shape Check Jig', 'Drawing', 'Inspection'];

        return view('operator.dashboard', compact(
            'todayRecords', 'draftRecords', 'todayBodyStats', 'myRecords',
            'shifts', 'processMains', 'process2Options'
        ));
    }
}
