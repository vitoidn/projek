<?php
namespace App\Services;

use App\Models\OperationalRecord;
use App\Models\ProductionPlanning;
use App\Models\Horenzo;

class HorenzoGeneratorService
{
    public function generate($date, $shift_id, $line_id)
    {
        $records = OperationalRecord::with(['details.defects', 'details.downtimes'])
            ->where('date', $date)
            ->where('shift_id', $shift_id)
            ->where('line_id', $line_id)
            ->get();
            
        $targetQty = ProductionPlanning::where('date', $date)
            ->where('shift_id', $shift_id)
            ->where('line_id', $line_id)
            ->sum('target_qty');

        $totalProduction = 0;
        $totalOk = 0;
        $totalNg = 0;
        $totalDowntimeSec = 0;
        $totalWorkingTimeSec = 0;

        foreach ($records as $record) {
            foreach ($record->details as $detail) {
                if ($detail->status == 'Finished') {
                    $totalProduction += $detail->qty_production;
                    $totalOk += $detail->qty_ok;
                    $totalNg += $detail->qty_ng;
                    $totalDowntimeSec += $detail->total_downtime_sec;
                    $totalWorkingTimeSec += $detail->working_time_sec;
                }
            }
        }

        $achievementPercent = $targetQty > 0 ? round(($totalProduction / $targetQty) * 100, 2) : 0;
        
        $horenzo = Horenzo::updateOrCreate(
            [
                'date' => $date,
                'shift_id' => $shift_id,
                'line_id' => $line_id,
            ],
            [
                'total_production' => $totalProduction,
                'total_ok' => $totalOk,
                'total_ng' => $totalNg,
                'total_downtime_sec' => $totalDowntimeSec,
                'total_working_time_sec' => $totalWorkingTimeSec,
                'achievement_percent' => $achievementPercent,
                'target_qty' => $targetQty
            ]
        );

        return $horenzo;
    }
}
