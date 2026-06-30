<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MActivityCode;

class ActivityCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['code' => '1', 'name' => 'Taisou'],
            ['code' => '2', 'name' => 'Briefing'],
            ['code' => '3', 'name' => 'Preparation Prod'],
            ['code' => '4', 'name' => 'Running'],
            ['code' => '5', 'name' => 'Trial'],
            ['code' => '6', 'name' => 'Maintenance'],
            ['code' => '7', 'name' => 'STOP'],
            ['code' => '8', 'name' => 'Change Model'],
            ['code' => '9', 'name' => 'ART-4S'],
            ['code' => '0', 'name' => 'No Plan'],
        ];

        foreach ($codes as $code) {
            MActivityCode::create($code);
        }
    }
}
