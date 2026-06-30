<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDefect;

class DefectController extends Controller
{
    public function index()
    {
        $defects = MasterDefect::latest()->get();
        return view('admin.defects.index', compact('defects'));
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:master_defects']);
        MasterDefect::create($request->all());
        return back()->with('success', 'Defect added successfully.');
    }
    public function update(Request $request, string $id)
    {
        $defect = MasterDefect::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255|unique:master_defects,name,' . $defect->id]);
        $defect->update($request->all());
        return back()->with('success', 'Defect updated successfully.');
    }
    public function destroy(string $id)
    {
        MasterDefect::findOrFail($id)->delete();
        return back()->with('success', 'Defect deleted successfully.');
    }
}
