<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterLine;
use App\Models\MasterShift;
use App\Models\Horenzo;
use App\Services\HorenzoGeneratorService;

class HorenzoController extends Controller
{
    public function index(Request $request, HorenzoGeneratorService $generator)
    {
        $lines = MasterLine::all();
        $shifts = MasterShift::all();
        $horenzo = null;

        if ($request->has(['date', 'line_id', 'shift_id']) && $request->date != null) {
            $horenzo = $generator->generate($request->date, $request->shift_id, $request->line_id);
            $horenzo->load(['line', 'shift']);
        }

        return view('supervisor.horenzo.index', compact('lines', 'shifts', 'horenzo'));
    }
}
