<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MPartCode;
use Illuminate\Http\Request;

class PartCodeController extends Controller
{
    public function index()
    {
        $partCodes = MPartCode::latest()->get();
        return view('admin.master.part-codes.index', compact('partCodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:100|unique:m_part_codes',
            'name' => 'nullable|string|max:255',
        ]);

        MPartCode::create($request->all());

        return back()->with('success', 'Part Code added successfully.');
    }

    public function update(Request $request, $id)
    {
        $partCode = MPartCode::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:100|unique:m_part_codes,code,' . $partCode->id,
            'name' => 'nullable|string|max:255',
        ]);

        $partCode->update($request->all());

        return back()->with('success', 'Part Code updated successfully.');
    }

    public function destroy($id)
    {
        MPartCode::findOrFail($id)->delete();
        return back()->with('success', 'Part Code deleted successfully.');
    }
}
