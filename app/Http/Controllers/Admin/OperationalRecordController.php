<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpRecordHeader;

class OperationalRecordController extends Controller
{
    public function index()
    {
        $records = OpRecordHeader::with(['shift', 'createdBy', 'bodies'])
            ->latest()
            ->paginate(20);

        return view('admin.op-record.index', compact('records'));
    }

    public function show($id)
    {
        $record = OpRecordHeader::with(['shift', 'createdBy', 'bodies.code'])
            ->findOrFail($id);

        return view('admin.op-record.show', compact('record'));
    }
}
