<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MActivityCode;
use Illuminate\Http\Request;

class ActivityCodeController extends Controller
{
    public function index()
    {
        $activityCodes = MActivityCode::orderBy('code')->get();
        return view('admin.master.activity-codes.index', compact('activityCodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:2|unique:m_activity_codes',
            'name' => 'required|string|max:255',
        ]);

        MActivityCode::create($request->all());

        return back()->with('success', 'Activity Code added successfully.');
    }

    public function update(Request $request, $id)
    {
        $activityCode = MActivityCode::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:2|unique:m_activity_codes,code,' . $activityCode->id,
            'name' => 'required|string|max:255',
        ]);

        $activityCode->update($request->all());

        return back()->with('success', 'Activity Code updated successfully.');
    }

    public function destroy($id)
    {
        MActivityCode::findOrFail($id)->delete();
        return back()->with('success', 'Activity Code deleted successfully.');
    }
}
