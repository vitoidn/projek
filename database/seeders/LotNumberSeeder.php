<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MLotNumber;

class LotNumberSeeder extends Seeder
{
    public function run(): void
    {
        $year = date('Y');
        $month = date('m');

        MLotNumber::create([
            'code' => 'P' . substr($year, 2) . $month,
            'year' => $year,
            'month' => $month,
            'is_active' => true,
        ]);
    }
}
