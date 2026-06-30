<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterLine;

class LineController extends Controller
{
    public function index()
    {
        $lines = MasterLine::latest()->get();
        return view('admin.lines.index', compact('lines'));
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:master_lines']);
        MasterLine::create($request->all());
        return back()->with('success', 'Line added successfully.');
    }
    public function update(Request $request, string $id)
    {
        $line = MasterLine::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255|unique:master_lines,name,' . $line->id]);
        $line->update($request->all());
        return back()->with('success', 'Line updated successfully.');
    }
    public function destroy(string $id)
    {
        MasterLine::findOrFail($id)->delete();
        return back()->with('success', 'Line deleted successfully.');
    }
}
