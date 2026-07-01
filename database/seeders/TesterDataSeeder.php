<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OpRecordHeader;
use App\Models\OpRecordBody;
use App\Models\MActivityCode;
use App\Models\MasterShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TesterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Cleanup existing tester data to allow re-run
        $testerEmails = ['ahmad@example.com', 'budi@example.com', 'supervisor@example.com'];
        $testerIds = User::whereIn('email', $testerEmails)->pluck('id');
        OpRecordHeader::whereIn('created_by', $testerIds)->each(fn($h) => $h->bodies()->delete());
        OpRecordHeader::whereIn('created_by', $testerIds)->delete();
        User::whereIn('email', $testerEmails)->delete();

        $shift = MasterShift::where('name', 'Shift 1')->first();
        $c = MActivityCode::pluck('id', 'code');

        // =============== USERS ===============
        $ahmad = User::firstOrCreate(
            ['email' => 'ahmad@example.com'],
            ['name' => 'Ahmad Rianto', 'nik' => '1209', 'password' => Hash::make('password')]
        );
        $ahmad->syncRoles('karyawan');

        $budi = User::firstOrCreate(
            ['email' => 'budi@example.com'],
            ['name' => 'Budi Santoso', 'nik' => '1527', 'password' => Hash::make('password')]
        );
        $budi->syncRoles('karyawan');

        $spv = User::firstOrCreate(
            ['email' => 'supervisor@example.com'],
            ['name' => 'Supervisor Produksi', 'nik' => '1001', 'password' => Hash::make('password')]
        );
        $spv->syncRoles('supervisor');

        $today = now()->toDateString();

        // ================================================================
        // HEADER 1: Ahmad — Manual Bending (Tono)
        // ================================================================
        $h1 = OpRecordHeader::create([
            'date' => $today,
            'shift_id' => $shift->id,
            'process_main' => 'Manual Bending',
            'process_2' => ['Manual Bending', 'Shape Check Jig', 'Drawing', 'Inspection'],
            'niks' => [
                'bending' => ['nik' => '2001', 'name' => 'Tono'],
                'shape_check_jig' => ['nik' => '2001', 'name' => 'Tono'],
                'drawing' => ['nik' => '2001', 'name' => 'Tono'],
                'inspection' => ['nik' => '2003', 'name' => 'Rina'],
            ],
            'status' => 'final',
            'created_by' => $ahmad->id,
        ]);

        $bodiesH1 = [
            ['', '', $c['2'], '07:50', '08:00', 10, 0, 0, 0, 'Briefing'],
            ['', '', $c['3'], '08:00', '08:10', 10, 0, 0, 0, 'Preparation'],
            ['', '', $c['8'], '08:10', '08:20', 10, 0, 0, 0, 'Change Model'],
            ['L6940', 'P2507-00001', $c['4'], '08:20', '08:55', 35, 12, 0, 0, ''],
            ['', '', $c['8'], '08:55', '09:05', 10, 0, 0, 0, ''],
            ['L6529', 'P2507-00002', $c['4'], '09:05', '09:35', 30, 36, 0, 0, ''],
            ['', '', $c['8'], '09:35', '09:45', 10, 0, 0, 0, ''],
            ['L6973', 'P2507-00003', $c['4'], '09:45', '10:15', 30, 12, 0, 0, ''],
            ['', '', $c['8'], '10:15', '10:25', 10, 0, 0, 0, 'Break 10'],
            ['', '', $c['8'], '10:25', '10:35', 10, 0, 0, 0, ''],
            ['L6962', 'P2507-00004', $c['4'], '10:35', '10:50', 15, 10, 0, 0, ''],
            ['', '', $c['8'], '10:50', '11:00', 10, 0, 0, 0, ''],
            ['L6989', 'P2507-00005', $c['4'], '11:00', '11:15', 15, 10, 0, 0, ''],
            ['', '', $c['7'], '11:15', '15:20', 245, 0, 0, 0, 'MP Running Auto Bending'],
            ['', '', $c['8'], '15:20', '15:35', 15, 0, 0, 0, 'Break 15'],
            ['', '', $c['7'], '15:35', '15:50', 15, 0, 0, 0, ''],
            ['L6965', 'P2507-00006', $c['4'], '15:55', '16:15', 20, 6, 0, 0, ''],
            ['', '', $c['8'], '16:15', '16:25', 10, 0, 0, 0, ''],
            ['L4382', 'P2601-00001', $c['4'], '16:25', '16:40', 15, 60, 0, 0, ''],
            ['L4382', 'P2601-00002', $c['4'], '16:40', '16:55', 15, 60, 0, 0, ''],
            ['', '', $c['9'], '16:55', '17:00', 5, 0, 0, 0, 'ART 45'],
        ];

        foreach ($bodiesH1 as $b) {
            $h1->bodies()->create([
                'part_code' => $b[0],
                'lot_id' => $b[1] ?: null,
                'code_id' => $b[2],
                'start_time' => $b[3],
                'end_time' => $b[4],
                'duration_min' => $b[5],
                'qty' => $b[6],
                'ng' => $b[7],
                'hold' => $b[8],
                'remark' => $b[9],
            ]);
        }

        // ================================================================
        // HEADER 2: Ahmad — Auto Bending (Chelsy)
        // ================================================================
        $h2 = OpRecordHeader::create([
            'date' => $today,
            'shift_id' => $shift->id,
            'process_main' => 'Auto Bending',
            'process_2' => ['Auto Bending', 'Shape Check Jig', 'Drawing', 'Inspection'],
            'niks' => [
                'bending' => ['nik' => '2002', 'name' => 'Chelsy'],
                'shape_check_jig' => ['nik' => '2002', 'name' => 'Chelsy'],
                'drawing' => ['nik' => '2002', 'name' => 'Chelsy'],
                'inspection' => ['nik' => '2003', 'name' => 'Rina'],
            ],
            'status' => 'final',
            'created_by' => $ahmad->id,
        ]);

        $bodiesH2 = [
            ['', '', $c['2'], '07:30', '07:45', 15, 0, 0, 0, 'Briefing'],
            ['', '', $c['3'], '07:45', '08:00', 15, 0, 0, 0, 'Preparation Prod'],
            ['', '', $c['8'], '08:00', '08:10', 10, 0, 0, 0, 'Setting'],
            ['M1234', 'P2507-00101', $c['4'], '08:10', '08:55', 45, 50, 0, 0, ''],
            ['M1234', 'P2507-00102', $c['4'], '08:55', '09:40', 45, 48, 0, 0, ''],
            ['', '', $c['8'], '09:40', '09:50', 10, 0, 0, 0, 'Change Model'],
            ['M5678', 'P2507-00103', $c['4'], '09:50', '10:35', 45, 60, 0, 0, ''],
            ['M5678', 'P2507-00104', $c['4'], '10:35', '11:20', 45, 55, 0, 0, ''],
            ['', '', $c['8'], '11:20', '11:30', 10, 0, 0, 0, 'Change Model'],
            ['M9012', 'P2507-00105', $c['4'], '11:30', '12:15', 45, 60, 0, 0, ''],
            ['M9012', 'P2507-00106', $c['4'], '12:15', '12:30', 15, 20, 0, 0, ''],
            ['', '', $c['7'], '12:30', '13:00', 30, 0, 0, 0, 'Istirahat'],
            ['M9012', 'P2507-00107', $c['4'], '13:00', '13:45', 45, 50, 0, 0, ''],
            ['', '', $c['8'], '13:45', '13:55', 10, 0, 0, 0, 'Change Model'],
            ['N3456', 'P2601-00101', $c['4'], '13:55', '14:40', 45, 40, 0, 0, ''],
            ['', '', $c['9'], '14:40', '14:50', 10, 0, 0, 0, 'ART 45'],
        ];

        foreach ($bodiesH2 as $b) {
            $h2->bodies()->create([
                'part_code' => $b[0],
                'lot_id' => $b[1] ?: null,
                'code_id' => $b[2],
                'start_time' => $b[3],
                'end_time' => $b[4],
                'duration_min' => $b[5],
                'qty' => $b[6],
                'ng' => $b[7],
                'hold' => $b[8],
                'remark' => $b[9],
            ]);
        }

        // ================================================================
        // HEADER 3: Budi — Manual Bending (Fajar, ada NG & Hold)
        // ================================================================
        $h3 = OpRecordHeader::create([
            'date' => $today,
            'shift_id' => $shift->id,
            'process_main' => 'Manual Bending',
            'process_2' => ['Manual Bending', 'Shape Check Jig', 'Drawing', 'Inspection'],
            'niks' => [
                'bending' => ['nik' => '3001', 'name' => 'Fajar'],
                'shape_check_jig' => ['nik' => '3001', 'name' => 'Fajar'],
                'drawing' => ['nik' => '3001', 'name' => 'Fajar'],
                'inspection' => ['nik' => '3003', 'name' => 'Hani'],
            ],
            'status' => 'final',
            'created_by' => $budi->id,
        ]);

        $bodiesH3 = [
            ['', '', $c['1'], '07:30', '07:40', 10, 0, 0, 0, 'Taisou'],
            ['', '', $c['2'], '07:40', '07:50', 10, 0, 0, 0, 'Briefing'],
            ['', '', $c['3'], '07:50', '08:00', 10, 0, 0, 0, 'Preparation'],
            ['', '', $c['8'], '08:00', '08:10', 10, 0, 0, 0, 'Setting'],
            ['X1001', 'P2507-00201', $c['4'], '08:10', '08:40', 30, 20, 0, 0, ''],
            ['', '', $c['8'], '08:40', '08:50', 10, 0, 0, 0, 'Change Model'],
            ['X1002', 'P2507-00202', $c['4'], '08:50', '09:35', 45, 30, 0, 0, ''],
            ['', '', $c['8'], '09:35', '09:45', 10, 0, 0, 0, ''],
            ['X1003', 'P2507-00203', $c['4'], '09:45', '10:15', 30, 24, 2, 0, 'Deform'],
            ['', '', $c['7'], '10:15', '10:30', 15, 0, 0, 0, 'Quality Check'],
            ['X1003', 'P2507-00204', $c['4'], '10:30', '11:00', 30, 24, 0, 0, ''],
            ['', '', $c['8'], '11:00', '11:10', 10, 0, 0, 0, ''],
            ['X1004', 'P2507-00205', $c['4'], '11:10', '11:40', 30, 18, 0, 0, ''],
            ['', '', $c['6'], '11:40', '12:00', 20, 0, 0, 0, 'Maintenance Die'],
            ['X1004', 'P2507-00206', $c['4'], '12:00', '12:30', 30, 18, 0, 0, ''],
            ['', '', $c['7'], '12:30', '13:00', 30, 0, 0, 0, 'Istirahat'],
            ['X1005', 'P2507-00207', $c['4'], '13:00', '13:30', 30, 15, 0, 0, ''],
            ['', '', $c['8'], '13:30', '13:40', 10, 0, 0, 0, ''],
            ['X1006', 'P2507-00208', $c['4'], '13:40', '14:10', 30, 20, 0, 0, ''],
            ['', '', $c['8'], '14:10', '14:20', 10, 0, 0, 0, ''],
            ['X1007', 'P2601-00201', $c['4'], '14:20', '14:50', 30, 30, 0, 0, ''],
            ['', '', $c['9'], '14:50', '15:00', 10, 0, 0, 0, 'ART 45'],
        ];

        foreach ($bodiesH3 as $b) {
            $h3->bodies()->create([
                'part_code' => $b[0],
                'lot_id' => $b[1] ?: null,
                'code_id' => $b[2],
                'start_time' => $b[3],
                'end_time' => $b[4],
                'duration_min' => $b[5],
                'qty' => $b[6],
                'ng' => $b[7],
                'hold' => $b[8],
                'remark' => $b[9],
            ]);
        }

        // ================================================================
        // HEADER 4: Budi — Auto Bending (Gita)
        // ================================================================
        $h4 = OpRecordHeader::create([
            'date' => $today,
            'shift_id' => $shift->id,
            'process_main' => 'Auto Bending',
            'process_2' => ['Auto Bending', 'Shape Check Jig', 'Drawing', 'Inspection'],
            'niks' => [
                'bending' => ['nik' => '3002', 'name' => 'Gita'],
                'shape_check_jig' => ['nik' => '3002', 'name' => 'Gita'],
                'drawing' => ['nik' => '3002', 'name' => 'Gita'],
                'inspection' => ['nik' => '3003', 'name' => 'Hani'],
            ],
            'status' => 'final',
            'created_by' => $budi->id,
        ]);

        $bodiesH4 = [
            ['', '', $c['2'], '08:00', '08:10', 10, 0, 0, 0, 'Briefing'],
            ['', '', $c['5'], '08:10', '08:40', 30, 5, 1, 0, 'Trial Part'],
            ['', '', $c['8'], '08:40', '08:50', 10, 0, 0, 0, 'Setting After Trial'],
            ['Y2001', 'P2507-00301', $c['4'], '08:50', '09:35', 45, 50, 0, 0, ''],
            ['Y2002', 'P2507-00302', $c['4'], '09:35', '10:20', 45, 50, 0, 0, ''],
            ['', '', $c['8'], '10:20', '10:30', 10, 0, 0, 0, 'Change Model'],
            ['Y2003', 'P2507-00303', $c['4'], '10:30', '11:15', 45, 48, 0, 2, 'Hold QC'],
            ['Y2003', 'P2507-00304', $c['4'], '11:15', '12:00', 45, 48, 0, 0, ''],
            ['', '', $c['7'], '12:00', '12:45', 45, 0, 0, 0, 'Istirahat'],
            ['Y2004', 'P2507-00305', $c['4'], '12:45', '13:30', 45, 50, 0, 0, ''],
            ['', '', $c['8'], '13:30', '13:40', 10, 0, 0, 0, 'Change Model'],
            ['Y2005', 'P2507-00306', $c['4'], '13:40', '14:25', 45, 50, 0, 0, ''],
            ['Y2005', 'P2507-00307', $c['4'], '14:25', '14:55', 30, 30, 0, 0, ''],
            ['', '', $c['9'], '14:55', '15:05', 10, 0, 0, 0, 'ART 45'],
        ];

        foreach ($bodiesH4 as $b) {
            $h4->bodies()->create([
                'part_code' => $b[0],
                'lot_id' => $b[1] ?: null,
                'code_id' => $b[2],
                'start_time' => $b[3],
                'end_time' => $b[4],
                'duration_min' => $b[5],
                'qty' => $b[6],
                'ng' => $b[7],
                'hold' => $b[8],
                'remark' => $b[9],
            ]);
        }

        $this->command->info('Tester data seeded successfully!');
    }
}
