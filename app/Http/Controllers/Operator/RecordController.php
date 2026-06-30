<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationalRecord;

class RecordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'nik' => 'required|string|max:50',
            'operator_name' => 'required|string|max:255',
            'line_id' => 'required|exists:master_lines,id',
            'shift_id' => 'required|exists:master_shifts,id',
        ]);

        $record = OperationalRecord::create([
            'date' => $request->date,
            'nik' => $request->nik,
            'operator_name' => $request->operator_name,
            'line_id' => $request->line_id,
            'shift_id' => $request->shift_id,
            'process' => 'Manual Bending',
        ]);

        return redirect()->route('operator.lot.index', $record->id)->with('success', 'Header Operational Record berhasil disimpan.');
    }
}
