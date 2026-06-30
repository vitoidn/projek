<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\MasterLine::insert([
            ['name' => 'Line A', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Line B', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Line C', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\MasterShift::insert([
            ['name' => 'Shift 1', 'start_time' => '07:00:00', 'end_time' => '15:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shift 2', 'start_time' => '15:00:00', 'end_time' => '23:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shift 3', 'start_time' => '23:00:00', 'end_time' => '07:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\MasterPart::insert([
            ['part_code' => 'Part A', 'lot_number' => 'A001', 'qty_per_lot' => 36, 'cycle_time_sec' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['part_code' => 'Part B', 'lot_number' => 'B001', 'qty_per_lot' => 60, 'cycle_time_sec' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\MasterDefect::insert([
            ['name' => 'Scratch', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dent', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Burr', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Crack', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deform', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\MasterDowntime::insert([
            ['name' => 'Istirahat', 'type' => 'Istirahat', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Material Habis', 'type' => 'Material', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Setting', 'type' => 'Setting', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Quality Check', 'type' => 'Quality', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lainnya', 'type' => 'Lainnya', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
