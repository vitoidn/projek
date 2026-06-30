<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterShift;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = MasterShift::latest()->get();
        return view('admin.shifts.index', compact('shifts'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:master_shifts',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);
        MasterShift::create($request->all());
        return back()->with('success', 'Shift added successfully.');
    }
    public function update(Request $request, string $id)
    {
        $shift = MasterShift::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:master_shifts,name,' . $shift->id,
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
        ]);
        $shift->update($request->all());
        return back()->with('success', 'Shift updated successfully.');
    }
    public function destroy(string $id)
    {
        MasterShift::findOrFail($id)->delete();
        return back()->with('success', 'Shift deleted successfully.');
    }
}
