<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPart;
use App\Models\MasterLine;
use App\Models\MasterShift;
use App\Models\MasterDefect;
use App\Models\MasterDowntime;
use App\Models\ProductionPlanning;
use App\Models\OperationalRecord;
use App\Models\OperationalRecordDetail;
use App\Models\OperationalRecordDowntime;
use App\Models\OperationalRecordDefect;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class MockProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing data to prevent duplications
        Schema::disableForeignKeyConstraints();
        OperationalRecordDefect::truncate();
        OperationalRecordDowntime::truncate();
        OperationalRecordDetail::truncate();
        OperationalRecord::truncate();
        ProductionPlanning::truncate();
        AuditLog::truncate();
        Schema::enableForeignKeyConstraints();

        $parts = MasterPart::all();
        $lines = MasterLine::all();
        $shifts = MasterShift::all();
        $defects = MasterDefect::all();
        $downtimes = MasterDowntime::all();

        if ($parts->isEmpty() || $lines->isEmpty() || $shifts->isEmpty()) {
            return;
        }

        // Generate data for past 7 days
        for ($dayOffset = 6; $dayOffset >= 0; $dayOffset--) {
            $currentDate = Carbon::today()->subDays($dayOffset)->toDateString();

            foreach ($lines as $line) {
                foreach ($shifts as $shift) {
                    $part = $parts->random();
                    $targetQty = rand(150, 300);
                    $qtyPerLot = $part->qty_per_lot;
                    $jumlahLot = ceil($targetQty / $qtyPerLot);

                    // 1. Create Production Planning
                    $planning = ProductionPlanning::create([
                        'date' => $currentDate,
                        'shift_id' => $shift->id,
                        'line_id' => $line->id,
                        'part_id' => $part->id,
                        'target_qty' => $targetQty,
                        'jumlah_lot' => $jumlahLot,
                        'created_at' => Carbon::parse($currentDate)->addHours(6),
                        'updated_at' => Carbon::parse($currentDate)->addHours(6),
                    ]);

                    // 2. Create Operational Record Header
                    $operators = [
                        ['nik' => 'NIK001', 'name' => 'Ahmad Rian'],
                        ['nik' => 'NIK002', 'name' => 'Budi Santoso'],
                        ['nik' => 'NIK003', 'name' => 'Candra Wijaya'],
                        ['nik' => 'NIK004', 'name' => 'Dedi Kurniawan'],
                    ];
                    $operator = $operators[array_rand($operators)];

                    $or = OperationalRecord::create([
                        'date' => $currentDate,
                        'nik' => $operator['nik'],
                        'operator_name' => $operator['name'],
                        'shift_id' => $shift->id,
                        'line_id' => $line->id,
                        'process' => 'Manual Bending',
                        'created_at' => Carbon::parse($currentDate)->addHours(7),
                        'updated_at' => Carbon::parse($currentDate)->addHours(7),
                    ]);

                    // 3. Create Details (Lots)
                    $currentShiftStart = Carbon::parse($currentDate . ' ' . $shift->start_time);
                    if ($shift->name === 'Shift 3' && Carbon::parse($shift->start_time)->hour >= 20) {
                        // Shift 3 starts late night of previous date
                        $currentShiftStart = Carbon::parse($currentDate . ' ' . $shift->start_time)->subDay();
                    }

                    for ($lotIndex = 1; $lotIndex <= $jumlahLot; $lotIndex++) {
                        $lotNo = 'L' . str_pad($lotIndex, 2, '0', STR_PAD_LEFT);
                        $lotNumberString = $part->lot_number . '-' . $lotNo;

                        // Is today and the last couple of lots?
                        $isToday = ($dayOffset === 0);
                        $isLastLotOfToday = $isToday && ($lotIndex === $jumlahLot);
                        $isSecondToLastLotOfToday = $isToday && ($lotIndex === $jumlahLot - 1);

                        $lotStartTime = $currentShiftStart->copy()->addMinutes(($lotIndex - 1) * 60);

                        $status = 'Finished';
                        $qtyProduction = $qtyPerLot;
                        $qtyNg = rand(0, 100) < 15 ? rand(1, 3) : 0; // 15% chance of defects
                        $qtyOk = $qtyProduction - $qtyNg;

                        $stdTimeSec = $qtyProduction * $part->cycle_time_sec;
                        $downtimeSec = 0;
                        $actualTimeSec = $stdTimeSec + rand(120, 480); // random extra seconds

                        if ($isLastLotOfToday) {
                            $status = 'Ready';
                            $qtyProduction = 0;
                            $qtyOk = 0;
                            $qtyNg = 0;
                            $actualTimeSec = null;
                            $stdTimeSec = null;
                            $lotStartTime = null;
                        } elseif ($isSecondToLastLotOfToday) {
                            $status = 'Running';
                            $qtyProduction = 0; // currently running
                            $qtyOk = 0;
                            $qtyNg = 0;
                            $actualTimeSec = null;
                            $stdTimeSec = null;
                        }

                        $detail = OperationalRecordDetail::create([
                            'or_id' => $or->id,
                            'part_id' => $part->id,
                            'lot_number' => $lotNumberString,
                            'qty_per_lot' => $qtyPerLot,
                            'cycle_time_sec' => $part->cycle_time_sec,
                            'start_time' => $lotStartTime,
                            'estimated_end' => $lotStartTime ? $lotStartTime->copy()->addSeconds($stdTimeSec ?? 3600) : null,
                            'actual_end' => ($status === 'Finished') ? $lotStartTime->copy()->addSeconds($actualTimeSec + $downtimeSec) : null,
                            'standard_time_sec' => $stdTimeSec,
                            'actual_time_sec' => $actualTimeSec,
                            'total_downtime_sec' => 0, // Will be updated if downtime added
                            'working_time_sec' => ($status === 'Finished') ? $actualTimeSec : null,
                            'production_status' => ($status === 'Finished') ? 'OK' : null,
                            'qty_production' => $qtyProduction,
                            'qty_ok' => $qtyOk,
                            'qty_ng' => $qtyNg,
                            'status' => $status,
                            'created_at' => Carbon::parse($currentDate)->addHours(7),
                            'updated_at' => Carbon::parse($currentDate)->addHours(7),
                        ]);

                        // Add downtime to some finished lots (approx 20% chance)
                        if ($status === 'Finished' && rand(0, 100) < 20) {
                            $dt = $downtimes->random();
                            $dtDuration = rand(300, 1200); // 5 to 20 minutes

                            $dtStart = $lotStartTime->copy()->addMinutes(10);
                            $dtEnd = $dtStart->copy()->addSeconds($dtDuration);

                            OperationalRecordDowntime::create([
                                'or_detail_id' => $detail->id,
                                'downtime_id' => $dt->id,
                                'start_time' => $dtStart,
                                'end_time' => $dtEnd,
                                'duration_sec' => $dtDuration,
                                'created_at' => Carbon::parse($currentDate)->addHours(7),
                                'updated_at' => Carbon::parse($currentDate)->addHours(7),
                            ]);

                            $detail->update([
                                'total_downtime_sec' => $dtDuration,
                                'actual_end' => $detail->actual_end->addSeconds($dtDuration)
                            ]);
                        }

                        // Add defect breakdown if qty_ng > 0
                        if ($status === 'Finished' && $qtyNg > 0) {
                            $df = $defects->random();
                            OperationalRecordDefect::create([
                                'or_detail_id' => $detail->id,
                                'defect_id' => $df->id,
                                'qty' => $qtyNg,
                                'created_at' => Carbon::parse($currentDate)->addHours(7),
                                'updated_at' => Carbon::parse($currentDate)->addHours(7),
                            ]);
                        }
                    }
                }
            }

            // Create some audit logs
            if (rand(0, 100) < 40) {
                AuditLog::create([
                    'table_name' => 'operational_records',
                    'record_id' => rand(1, 10),
                    'old_value' => json_encode(['operator_name' => 'Old Operator Name']),
                    'new_value' => json_encode(['operator_name' => 'New Operator Name']),
                    'changed_by' => 'Spv Produksi',
                    'reason' => 'Koreksi kesalahan input nama operator di line ' . $lines->random()->name,
                    'created_at' => Carbon::parse($currentDate)->addHours(14),
                    'updated_at' => Carbon::parse($currentDate)->addHours(14),
                ]);
            }
        }
    }
}
