<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use App\Models\MasterShift;
use App\Models\MActivityCode;
use App\Models\MPartCode;
use Illuminate\Http\Request;

class OperationalRecordController extends Controller
{
    public function index()
    {
        $records = OpRecordHeader::with(['shift', 'bodies.code'])
            ->withCount('bodies')
            ->where('created_by', auth()->id())
            ->latest()
            ->paginate(20);

        $activityCodes = MActivityCode::orderBy('code')->get();

        return view('operator.op-record.index', compact('records', 'activityCodes'));
    }

    public function create()
    {
        $shifts = MasterShift::orderBy('name')->get();
        $processMains = ['Manual Bending', 'Auto Bending'];
        $process2Options = ['Shape Check Jig', 'Drawing', 'Inspection'];

        return view('operator.op-record.create', compact('shifts', 'processMains', 'process2Options'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift_id' => 'required|exists:master_shifts,id',
            'process_main' => 'required|string|in:Manual Bending,Auto Bending',
            'process_2' => 'nullable|array',
            'process_2.*' => 'string|in:Shape Check Jig,Drawing,Inspection',
            'niks' => 'required|array',
            'niks.*.nik' => 'nullable|string|max:50',
            'niks.*.name' => 'nullable|string|max:255',
            'prepare_signature' => 'nullable|string',
        ]);

        $header = OpRecordHeader::create([
            'date' => $request->date,
            'shift_id' => $request->shift_id,
            'process_main' => $request->process_main,
            'process_2' => $request->process_2 ?? [],
            'niks' => $this->buildNiks($request),
            'prepare_signature' => $request->prepare_signature,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('operator.op-record.index')
            ->with('success', 'Header berhasil dibuat. Silakan isi detail aktivitas (Body).');
    }

    public function show($id)
    {
        $record = OpRecordHeader::with(['shift', 'createdBy', 'bodies.code'])
            ->findOrFail($id);

        return view('operator.op-record.show', compact('record'));
    }

    public function edit($id)
    {
        $record = OpRecordHeader::with(['shift', 'bodies.code'])
            ->findOrFail($id);

        if ($record->status === 'final') {
            return redirect()->route('operator.op-record.show', $id)
                ->with('error', 'Record sudah Final, tidak bisa diedit.');
        }

        $activityCodes = MActivityCode::orderBy('code')->get();
        $partCodes = MPartCode::orderBy('code')->get();
        $shifts = MasterShift::orderBy('name')->get();
        $processMains = ['Manual Bending', 'Auto Bending'];
        $process2Options = ['Shape Check Jig', 'Drawing', 'Inspection'];

        return view('operator.op-record.edit', compact(
            'record', 'activityCodes', 'partCodes', 'shifts', 'processMains', 'process2Options'
        ));
    }

    public function update(Request $request, $id)
    {
        $record = OpRecordHeader::findOrFail($id);

        if ($record->status === 'final') {
            return back()->with('error', 'Record sudah Final, tidak bisa diedit.');
        }

        $request->validate([
            'date' => 'required|date',
            'shift_id' => 'required|exists:master_shifts,id',
            'process_main' => 'required|string|in:Manual Bending,Auto Bending',
            'process_2' => 'nullable|array',
            'process_2.*' => 'string|in:Shape Check Jig,Drawing,Inspection',
            'niks' => 'required|array',
            'niks.*.nik' => 'nullable|string|max:50',
            'niks.*.name' => 'nullable|string|max:255',
            'prepare_signature' => 'nullable|string',
        ]);

        $record->update([
            'date' => $request->date,
            'shift_id' => $request->shift_id,
            'process_main' => $request->process_main,
            'process_2' => $request->process_2 ?? [],
            'niks' => $this->buildNiks($request),
            'prepare_signature' => $request->prepare_signature,
        ]);

        return redirect()->route('operator.op-record.edit', $id)
            ->with('success', 'Header berhasil diupdate.');
    }

    public function submit($id)
    {
        $record = OpRecordHeader::withCount('bodies')->findOrFail($id);

        if ($record->bodies_count < 1) {
            return back()->with('error', 'Minimal 1 baris Body harus diisi sebelum submit.');
        }

        $record->update(['status' => 'final']);

        return redirect()->route('operator.op-record.show', $id)
            ->with('success', 'Record berhasil di-Final-kan.');
    }

    private function buildNiks(Request $request): array
    {
        $keys = [
            'shape_check_jig', 'drawing_inspection',
            'drawing', 'inspection',
        ];

        $niks = [];
        foreach ($keys as $key) {
            $nikVal = $request->input("niks.{$key}.nik");
            if ($nikVal) {
                $niks[$key] = [
                    'nik' => $nikVal,
                    'name' => $request->input("niks.{$key}.name", ''),
                ];
            } else {
                $niks[$key] = null;
            }
        }

        return $niks;
    }
}
