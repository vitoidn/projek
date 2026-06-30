<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BodyController extends Controller
{
    public function store(Request $request, $id)
    {
        $record = OpRecordHeader::findOrFail($id);

        if ($record->status === 'final') {
            return response()->json(['error' => 'Record sudah Final.'], 400);
        }

        $data = $request->only(['part_code', 'lot_id', 'code_id', 'start_time', 'end_time', 'qty', 'ng', 'hold', 'remark']);

        if ($request->filled('code_id') && $request->filled('start_time') && $request->filled('end_time')) {
            $request->validate([
                'code_id' => 'required|exists:m_activity_codes,id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => [
                    'required',
                    'date_format:H:i',
                    function ($attr, $value, $fail) use ($request) {
                        if ($request->start_time && $value <= $request->start_time) {
                            $fail('The end time must be after the start time.');
                        }
                    },
                ],
            ]);
            $start = Carbon::parse($request->start_time);
            $end = Carbon::parse($request->end_time);
            $data['duration_min'] = $start->diffInMinutes($end);
        }

        $data['header_id'] = $record->id;
        $data['part_code'] = $request->part_code ?? '';
        $data['qty'] = $request->qty ?? 0;
        $data['ng'] = $request->ng ?? 0;
        $data['hold'] = $request->hold ?? 0;

        $body = OpRecordBody::create($data);

        $body->load(['code']);

        return response()->json([
            'success' => true,
            'data' => $body,
        ]);
    }

    public function update(Request $request, $id, $bodyId)
    {
        $record = OpRecordHeader::findOrFail($id);

        if ($record->status === 'final') {
            return response()->json(['error' => 'Record sudah Final.'], 400);
        }

        $body = OpRecordBody::where('header_id', $record->id)->findOrFail($bodyId);

        if ($request->filled('code_id') && $request->filled('start_time') && $request->filled('end_time')) {
            $request->validate([
                'code_id' => 'required|exists:m_activity_codes,id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => [
                    'required',
                    'date_format:H:i',
                    function ($attr, $value, $fail) use ($request) {
                        if ($request->start_time && $value <= $request->start_time) {
                            $fail('The end time must be after the start time.');
                        }
                    },
                ],
            ]);
            $start = Carbon::parse($request->start_time);
            $end = Carbon::parse($request->end_time);
            $body->duration_min = $start->diffInMinutes($end);
        }

        $body->part_code = $request->part_code ?? '';
        $body->lot_id = $request->lot_id;
        $body->code_id = $request->code_id;
        $body->start_time = $request->start_time;
        $body->end_time = $request->end_time;
        $body->qty = $request->qty ?? 0;
        $body->ng = $request->ng ?? 0;
        $body->hold = $request->hold ?? 0;
        $body->remark = $request->remark;
        $body->save();

        return response()->json(['success' => true, 'data' => $body]);
    }

    public function destroy($id, $bodyId)
    {
        $record = OpRecordHeader::findOrFail($id);

        if ($record->status === 'final') {
            return response()->json(['error' => 'Record sudah Final.'], 400);
        }

        OpRecordBody::where('header_id', $record->id)
            ->where('id', $bodyId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
