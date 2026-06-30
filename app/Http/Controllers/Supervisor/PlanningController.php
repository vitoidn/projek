<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    public function index()
    {
        $plannings = \App\Models\ProductionPlanning::with(['shift', 'line', 'part'])->latest('date')->get();
        $shifts = \App\Models\MasterShift::orderBy('name')->get();
        $lines = \App\Models\MasterLine::orderBy('name')->get();
        $parts = \App\Models\MasterPart::orderBy('part_code')->get();
        return view('supervisor.planning.index', compact('plannings', 'shifts', 'lines', 'parts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift_id' => 'required|exists:master_shifts,id',
            'line_id' => 'required|exists:master_lines,id',
            'part_id' => 'required|exists:master_parts,id',
            'target_qty' => 'required|integer|min:1',
        ]);

        $part = \App\Models\MasterPart::findOrFail($request->part_id);
        $jumlah_lot = ceil($request->target_qty / $part->qty_per_lot);

        \App\Models\ProductionPlanning::create([
            'date' => $request->date,
            'shift_id' => $request->shift_id,
            'line_id' => $request->line_id,
            'part_id' => $request->part_id,
            'target_qty' => $request->target_qty,
            'jumlah_lot' => $jumlah_lot,
        ]);

        return back()->with('success', 'Production planning added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $planning = \App\Models\ProductionPlanning::findOrFail($id);
        $request->validate([
            'date' => 'required|date',
            'shift_id' => 'required|exists:master_shifts,id',
            'line_id' => 'required|exists:master_lines,id',
            'part_id' => 'required|exists:master_parts,id',
            'target_qty' => 'required|integer|min:1',
        ]);

        $part = \App\Models\MasterPart::findOrFail($request->part_id);
        $jumlah_lot = ceil($request->target_qty / $part->qty_per_lot);

        $planning->update([
            'date' => $request->date,
            'shift_id' => $request->shift_id,
            'line_id' => $request->line_id,
            'part_id' => $request->part_id,
            'target_qty' => $request->target_qty,
            'jumlah_lot' => $jumlah_lot,
        ]);

        return back()->with('success', 'Production planning updated successfully.');
    }

    public function destroy(string $id)
    {
        \App\Models\ProductionPlanning::findOrFail($id)->delete();
        return back()->with('success', 'Production planning deleted successfully.');
    }
}
