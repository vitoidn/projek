<?php
namespace App\Services;

class ProductionCalculatorService
{
    /**
     * Calculate Standard Time (seconds) based on Qty Production and Cycle Time
     */
    public function calculateStandardTime($cycleTimeSec, $qtyProduction)
    {
        return $cycleTimeSec * $qtyProduction;
    }

    /**
     * Calculate Actual Time (seconds) based on Start Time and Actual End Time
     */
    public function calculateActualTime($startTime, $actualEndTime)
    {
        if (!$startTime || !$actualEndTime) {
            return 0;
        }
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($actualEndTime);
        return $start->diffInSeconds($end);
    }

    /**
     * Calculate Working Time (Actual Time - Total Downtime)
     */
    public function calculateWorkingTime($actualTimeSec, $totalDowntimeSec)
    {
        $workingTime = $actualTimeSec - $totalDowntimeSec;
        return $workingTime > 0 ? $workingTime : 0;
    }

    /**
     * Determine Production Status (Faster, On Time, Late)
     */
    public function determineProductionStatus($workingTimeSec, $standardTimeSec)
    {
        if ($workingTimeSec < $standardTimeSec) {
            return 'Faster';
        } elseif ($workingTimeSec == $standardTimeSec) {
            return 'On Time';
        } else {
            return 'Late';
        }
    }
}
