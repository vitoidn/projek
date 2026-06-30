<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;
use Illuminate\Http\Request;

class RecordReportController extends Controller
{
    public function index()
    {
        $records = OpRecordHeader::with(['shift'])
            ->withCount('bodies')
            ->latest()
            ->paginate(20);

        return view('operator.report-record.index', compact('records'));
    }

    public function preview($id)
    {
        $record = OpRecordHeader::with(['bodies.code', 'shift', 'createdBy'])
            ->findOrFail($id);

        return view('operator.report-record.preview', compact('record'));
    }
}
