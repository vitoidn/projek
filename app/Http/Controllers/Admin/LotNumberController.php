<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MLotNumber;
use Illuminate\Http\Request;

class LotNumberController extends Controller
{
    public function index()
    {
        $lotNumbers = MLotNumber::latest()->get();
        return view('admin.master.lot-numbers.index', compact('lotNumbers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:m_lot_numbers',
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|string|min:2|max:2',
        ]);

        MLotNumber::create($request->all());

        return back()->with('success', 'Lot Number added successfully.');
    }

    public function update(Request $request, $id)
    {
        $lotNumber = MLotNumber::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:10|unique:m_lot_numbers,code,' . $lotNumber->id,
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|string|min:2|max:2',
            'is_active' => 'nullable|boolean',
        ]);

        $lotNumber->update($request->all());

        return back()->with('success', 'Lot Number updated successfully.');
    }

    public function destroy($id)
    {
        MLotNumber::findOrFail($id)->delete();
        return back()->with('success', 'Lot Number deleted successfully.');
    }
}
