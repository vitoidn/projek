<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use App\Models\MasterShift;
use App\Models\MActivityCode;
use App\Models\MLotNumber;
use App\Models\MPartCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HorenzoController extends Controller
{
    public function index(Request $request)
    {
        $shifts = MasterShift::all();
        $processMains = ['Manual Bending', 'Auto Bending'];
        $activityCodes = MActivityCode::all();
        $lotNumbers = MLotNumber::where('is_active', true)->get();
        $partCodes = MPartCode::all();
        $report = null;

        if ($request->has('date_from')) {
            $report = $this->generateReport($request);
        }

        return view('operator.horenzo.index', compact(
            'shifts', 'processMains', 'activityCodes', 'lotNumbers', 'partCodes', 'report'
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

        return redirect()->route('operator.horenzo.index', $request->only([
            'date_from', 'date_to', 'shift_id', 'process_main', 'part_code', 'lot_id', 'nik'
        ]))->with('success', 'Report Horenzo berhasil digenerate.');
    }

    private function generateReport(Request $request)
    {
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
            DB::raw('COUNT(DISTINCT op_record_headers.id) as total_headers'),
            DB::raw('COUNT(op_record_bodies.id) as total_rows'),
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

        $matchingHeaderIds = (clone $query)->select('op_record_headers.id')->distinct()->get()->pluck('id');
        $matchingHeaders = OpRecordHeader::whereIn('id', $matchingHeaderIds)
            ->get(['process_main', 'process_2', 'niks']);

        $process2List = [];
        foreach ($matchingHeaders as $h) {
            $p2 = is_array($h->process_2) ? $h->process_2 : [];
            foreach ($p2 as $v) {
                $v = trim($v);
                if (!empty($v) && !in_array($v, $process2List)) $process2List[] = $v;
            }
        }

        $nikSummary = [];
        foreach ($matchingHeaders as $h) {
            $niks = is_string($h->niks) ? json_decode($h->niks, true) : ($h->niks ?? []);
            if (is_array($niks)) {
                foreach ($niks as $key => $val) {
                    if ($val && !empty($val['nik'])) {
                        $groupKey = $h->process_main . '|' . $key;
                        if (!isset($nikSummary[$groupKey])) $nikSummary[$groupKey] = [];
                        $entry = $val['nik'] . (!empty($val['name']) ? ' (' . $val['name'] . ')' : '');
                        if (!in_array($entry, $nikSummary[$groupKey])) $nikSummary[$groupKey][] = $entry;
                    }
                }
            }
        }

        return (object) [
            'snapshot_data' => [
                'summary' => $summary,
                'per_code' => $perCode,
                'per_part' => $perPart,
                'process_2' => $process2List,
                'nik_per_process' => $nikSummary,
            ],
        ];
    }
}
