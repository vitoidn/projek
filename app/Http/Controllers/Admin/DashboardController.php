<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use App\Models\MasterShift;
use App\Models\MLotNumber;
use App\Models\MActivityCode;
use App\Models\MPartCode;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalShifts = MasterShift::count();
        $totalLotNumbers = MLotNumber::count();
        $totalActivityCodes = MActivityCode::count();
        $totalPartCodes = MPartCode::count();

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

        $trendLabels = [];
        $trendQty = [];
        $trendNg = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $trendLabels[] = Carbon::parse($date)->format('d M');

            $dayData = OpRecordBody::query()
                ->join('op_record_headers', 'op_record_bodies.header_id', '=', 'op_record_headers.id')
                ->where('op_record_headers.date', $date)
                ->select([
                    DB::raw('COALESCE(SUM(op_record_bodies.qty), 0) as total_qty'),
                    DB::raw('COALESCE(SUM(op_record_bodies.ng), 0) as total_ng'),
                ])
                ->first();

            $trendQty[] = (int) ($dayData->total_qty ?? 0);
            $trendNg[] = (int) ($dayData->total_ng ?? 0);
        }

        $activityBreakdown = OpRecordBody::query()
            ->join('m_activity_codes', 'op_record_bodies.code_id', '=', 'm_activity_codes.id')
            ->select([
                'm_activity_codes.name',
                DB::raw('SUM(op_record_bodies.duration_min) as total_duration_min'),
            ])
            ->groupBy('m_activity_codes.name')
            ->orderBy('total_duration_min', 'desc')
            ->take(5)
            ->get();

        $activityLabels = [];
        $activityDurations = [];
        foreach ($activityBreakdown as $row) {
            $activityLabels[] = $row->name;
            $activityDurations[] = (int) $row->total_duration_min;
        }

        $recentLogs = AuditLog::orderBy('created_at', 'desc')->take(5)->get();

        $recentRecords = OpRecordHeader::with(['shift', 'createdBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalShifts', 'totalLotNumbers', 'totalActivityCodes', 'totalPartCodes',
            'recordsToday', 'recordsFinalToday',
            'todaySummary',
            'trendLabels', 'trendQty', 'trendNg',
            'activityLabels', 'activityDurations',
            'recentLogs', 'recentRecords', 'today'
        ));
    }
}
