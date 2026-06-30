<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpRecordBody;
use App\Models\HorenzoReport;
use App\Models\MasterShift;
use App\Models\MActivityCode;
use App\Models\MLotNumber;
use App\Models\MPartCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HorenzoController extends Controller
{
    public function index()
    {
        $shifts = MasterShift::all();
        $processMains = ['Manual Bending', 'Auto Bending', 'Shape Check Jig', 'Drawing & Inspection'];
        $activityCodes = MActivityCode::all();
        $lotNumbers = MLotNumber::where('is_active', true)->get();
        $partCodes = MPartCode::all();
        $reports = HorenzoReport::with('generatedBy')->latest()->paginate(10);

        return view('admin.horenzo.index', compact(
            'shifts', 'processMains', 'activityCodes', 'lotNumbers', 'partCodes', 'reports'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'shift_id' => 'nullable|exists:master_shifts,id',
            'process_main' => 'nullable|string',
            'part_code' => 'nullable|string|max:100',
            'lot_id' => 'nullable|exists:m_lot_numbers,id',
            'nik' => 'nullable|string|max:255',
        ]);

        $query = OpRecordBody::query()
            ->join('op_record_headers', 'op_record_bodies.header_id', '=', 'op_record_headers.id')
            ->join('m_activity_codes', 'op_record_bodies.code_id', '=', 'm_activity_codes.id')
            ->leftJoin('m_lot_numbers', 'op_record_bodies.lot_id', '=', 'm_lot_numbers.id')
            ->whereBetween('op_record_headers.date', [$request->date_from, $request->date_to])
            ->where('op_record_headers.status', 'final');

        if ($request->shift_id) {
            $query->where('op_record_headers.shift_id', $request->shift_id);
        }

        if ($request->process_main) {
            $query->where('op_record_headers.process_main', $request->process_main);
        }

        if ($request->part_code) {
            $query->where('op_record_bodies.part_code', $request->part_code);
        }

        if ($request->lot_id) {
            $query->where('op_record_bodies.lot_id', $request->lot_id);
        }

        if ($request->nik) {
            $query->where('op_record_headers.niks', 'like', '%' . $request->nik . '%');
        }

        $summary = (clone $query)->select([
            DB::raw('SUM(op_record_bodies.qty) as total_qty'),
            DB::raw('SUM(op_record_bodies.ng) as total_ng'),
            DB::raw('SUM(op_record_bodies.hold) as total_hold'),
            DB::raw('SUM(op_record_bodies.duration_min) as total_duration_min'),
        ])->first();

        $perCode = (clone $query)
            ->select([
                'm_activity_codes.code as activity_code',
                'm_activity_codes.name as activity_name',
                DB::raw('SUM(op_record_bodies.duration_min) as total_duration_min'),
                DB::raw('COUNT(op_record_bodies.id) as total_records'),
            ])
            ->groupBy('m_activity_codes.code', 'm_activity_codes.name')
            ->orderBy('total_duration_min', 'desc')
            ->get();

        $perPart = (clone $query)
            ->select([
                'op_record_bodies.part_code',
                DB::raw('SUM(op_record_bodies.qty) as total_qty'),
                DB::raw('SUM(op_record_bodies.ng) as total_ng'),
                DB::raw('SUM(op_record_bodies.duration_min) as total_duration_min'),
            ])
            ->groupBy('op_record_bodies.part_code')
            ->orderBy('total_qty', 'desc')
            ->get();

        $snapshot = [
            'filter' => $request->only(['date_from', 'date_to', 'shift_id', 'process_main', 'part_code', 'lot_id', 'nik']),
            'summary' => $summary,
            'per_code' => $perCode,
            'per_part' => $perPart,
        ];

        $report = HorenzoReport::create([
            'filter_params' => $request->only(['date_from', 'date_to', 'shift_id', 'process_main', 'part_code', 'lot_id', 'nik']),
            'generated_by' => auth()->id(),
            'snapshot_data' => $snapshot,
        ]);

        return redirect()->route('admin.horenzo.index')->with('success', 'Horenzo report generated successfully.');
    }

    public function export($id)
    {
        $report = HorenzoReport::with('generatedBy')->findOrFail($id);
        // For now, redirect back. Export logic will be added later.
        return redirect()->route('admin.horenzo.index')->with('info', 'Export feature coming soon.');
    }
}
