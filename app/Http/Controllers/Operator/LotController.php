<?php
namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationalRecord;
use App\Models\OperationalRecordDetail;
use App\Models\MasterPart;
use App\Services\ProductionCalculatorService;
use Carbon\Carbon;

class LotController extends Controller
{
    protected $calculator;

    public function __construct(ProductionCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function index($or_id)
    {
        $record = OperationalRecord::with(['line', 'shift'])->findOrFail($or_id);
        $lots = OperationalRecordDetail::with('part')->where('or_id', $or_id)->latest()->get();
        $parts = MasterPart::orderBy('part_code')->get();
        return view('operator.lot.index', compact('record', 'lots', 'parts'));
    }

    public function store(Request $request, $or_id)
    {
        $request->validate([
            'part_id' => 'required|exists:master_parts,id',
            'lot_number' => 'required|string|max:255',
        ]);

        $part = MasterPart::findOrFail($request->part_id);

        $lot = OperationalRecordDetail::create([
            'or_id' => $or_id,
            'part_id' => $part->id,
            'lot_number' => $request->lot_number,
            'qty_per_lot' => $part->qty_per_lot,
            'cycle_time_sec' => $part->cycle_time_sec,
            'standard_time_sec' => $this->calculator->calculateStandardTime($part->cycle_time_sec, $part->qty_per_lot),
            'status' => 'Ready'
        ]);

        return redirect()->route('operator.lot.execute', ['or_id' => $or_id, 'id' => $lot->id]);
    }

    public function execute($or_id, $id)
    {
        $record = OperationalRecord::findOrFail($or_id);
        $lot = OperationalRecordDetail::with(['part', 'downtimes.downtime', 'defects'])->findOrFail($id);
        
        $downtimesMaster = \App\Models\MasterDowntime::orderBy('name')->get();
        $defectsMaster = \App\Models\MasterDefect::orderBy('name')->get();
        
        return view('operator.lot.execute', compact('record', 'lot', 'downtimesMaster', 'defectsMaster'));
    }

    // AJAX API start
    public function start(Request $request, $id)
    {
        $lot = OperationalRecordDetail::findOrFail($id);
        if ($lot->status !== 'Ready') {
            return response()->json(['error' => 'Lot is already started or finished.'], 400);
        }

        $now = Carbon::now();
        $estimatedEnd = $now->copy()->addSeconds($lot->standard_time_sec);

        $lot->update([
            'start_time' => $now,
            'estimated_end' => $estimatedEnd,
            'status' => 'Running'
        ]);

        return response()->json(['success' => true, 'start_time' => $now, 'estimated_end' => $estimatedEnd]);
    }

    // AJAX API finish
    public function finish(Request $request, $id)
    {
        $lot = OperationalRecordDetail::findOrFail($id);
        
        $request->validate([
            'qty_ok' => 'required|integer|min:0',
            'qty_ng' => 'required|integer|min:0',
            'defects' => 'nullable|array' // e.g. defects[defect_id] = qty
        ]);

        $qtyProduction = $request->qty_ok + $request->qty_ng;
        $now = Carbon::now();
        
        // Calculate Times
        $actualTimeSec = $this->calculator->calculateActualTime($lot->start_time, $now);
        $workingTimeSec = $this->calculator->calculateWorkingTime($actualTimeSec, $lot->total_downtime_sec);
        
        // Re-calculate standard time in case they produced less/more than lot_qty
        $actualStandardTimeSec = $this->calculator->calculateStandardTime($lot->cycle_time_sec, $qtyProduction);
        $productionStatus = $this->calculator->determineProductionStatus($workingTimeSec, $actualStandardTimeSec);

        $lot->update([
            'actual_end' => $now,
            'actual_time_sec' => $actualTimeSec,
            'working_time_sec' => $workingTimeSec,
            'qty_production' => $qtyProduction,
            'qty_ok' => $request->qty_ok,
            'qty_ng' => $request->qty_ng,
            'production_status' => $productionStatus,
            'status' => 'Finished'
        ]);

        // Save defects
        if ($request->has('defects')) {
            foreach ($request->defects as $defect_id => $qty) {
                if ($qty > 0) {
                    \App\Models\OperationalRecordDefect::create([
                        'or_detail_id' => $lot->id,
                        'defect_id' => $defect_id,
                        'qty' => $qty
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
