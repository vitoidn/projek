<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterLine;
use App\Models\ProductionPlanning;
use App\Models\OperationalRecord;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $lines = MasterLine::all();
        $lineData = [];
        
        $totalTargetDay = ProductionPlanning::where('date', $today)->sum('target_qty');
        $totalActualDay = 0;
        
        foreach ($lines as $line) {
            $target = ProductionPlanning::where('date', $today)->where('line_id', $line->id)->sum('target_qty');
            
            $records = OperationalRecord::with('details')
                ->where('date', $today)
                ->where('line_id', $line->id)
                ->get();
                
            $actual = 0;
            $runningLots = 0;
            
            foreach ($records as $r) {
                foreach ($r->details as $d) {
                    if ($d->status == 'Finished') {
                        $actual += $d->qty_production;
                    } elseif ($d->status == 'Running') {
                        $runningLots++;
                    }
                }
            }
            
            $totalActualDay += $actual;
            $achievment = $target > 0 ? round(($actual / $target) * 100, 1) : 0;
            
            $lineData[] = [
                'name' => $line->name,
                'target' => $target,
                'actual' => $actual,
                'achievement' => $achievment,
                'running_lots' => $runningLots
            ];
        }
        
        $dayAchievement = $totalTargetDay > 0 ? round(($totalActualDay / $totalTargetDay) * 100, 1) : 0;

        return view('supervisor.dashboard', compact('lineData', 'totalTargetDay', 'totalActualDay', 'dayAchievement', 'today'));
    }
}
