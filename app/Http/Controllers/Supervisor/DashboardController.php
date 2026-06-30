<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use App\Models\MasterShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        $recordsToday = OpRecordHeader::where('date', $today)->count();
        $recordsFinalToday = OpRecordHeader::where('date', $today)->where('status', 'final')->count();

        $todaySummary = OpRecordBody::query()
            ->join('op_record_headers', 'op_record_bodies.header_id', '=', 'op_record_headers.id')
            ->where('op_record_headers.date', $today)
            ->select([
                DB::raw('SUM(op_record_bodies.qty) as total_qty'),
                DB::raw('SUM(op_record_bodies.ng) as total_ng'),
                DB::raw('SUM(op_record_bodies.duration_min) as total_duration_min'),
            ])
            ->first();

        $recentRecords = OpRecordHeader::with(['shift', 'createdBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('supervisor.dashboard', compact(
            'recordsToday', 'recordsFinalToday', 'todaySummary', 'recentRecords', 'today'
        ));
    }
}
