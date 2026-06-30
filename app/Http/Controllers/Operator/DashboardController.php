<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $lines = \App\Models\MasterLine::orderBy('name')->get();
        $shifts = \App\Models\MasterShift::orderBy('name')->get();
        return view('operator.dashboard', compact('lines', 'shifts'));
    }
}
