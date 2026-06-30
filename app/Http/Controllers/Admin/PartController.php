<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterPart;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parts = MasterPart::latest()->get();
        return view('admin.parts.index', compact('parts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'part_code' => 'required|string|max:255|unique:master_parts',
            'lot_number' => 'nullable|string|max:255',
            'qty_per_lot' => 'required|integer|min:1',
            'cycle_time_sec' => 'required|integer|min:1',
        ]);
        MasterPart::create($request->all());
        return back()->with('success', 'Part added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $part = MasterPart::findOrFail($id);
        $request->validate([
            'part_code' => 'required|string|max:255|unique:master_parts,part_code,' . $part->id,
            'lot_number' => 'nullable|string|max:255',
            'qty_per_lot' => 'required|integer|min:1',
            'cycle_time_sec' => 'required|integer|min:1',
        ]);
        $part->update($request->all());
        return back()->with('success', 'Part updated successfully.');
    }

    public function destroy(string $id)
    {
        MasterPart::findOrFail($id)->delete();
        return back()->with('success', 'Part deleted successfully.');
    }
}
