<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDowntime;

class DowntimeController extends Controller
{
    public function index()
    {
        $downtimes = MasterDowntime::latest()->get();
        return view('admin.downtimes.index', compact('downtimes'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:master_downtimes',
            'type' => 'nullable|string|max:255',
        ]);
        MasterDowntime::create($request->all());
        return back()->with('success', 'Downtime added successfully.');
    }
    public function update(Request $request, string $id)
    {
        $downtime = MasterDowntime::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:master_downtimes,name,' . $downtime->id,
            'type' => 'nullable|string|max:255',
        ]);
        $downtime->update($request->all());
        return back()->with('success', 'Downtime updated successfully.');
    }
    public function destroy(string $id)
    {
        MasterDowntime::findOrFail($id)->delete();
        return back()->with('success', 'Downtime deleted successfully.');
    }
}
