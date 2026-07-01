<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roleKaryawan = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'karyawan', 'guard_name' => 'web']);
        $roleSupervisor = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);

        $karyawan = \App\Models\User::firstOrCreate(
            ['email' => 'karyawan@example.com'],
            ['name' => 'Karyawan Inspeksi', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );
        if (!$karyawan->hasRole('karyawan')) $karyawan->assignRole($roleKaryawan);

        $supervisor = \App\Models\User::firstOrCreate(
            ['email' => 'spv@example.com'],
            ['name' => 'Spv Produksi', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );
        if (!$supervisor->hasRole('supervisor')) $supervisor->assignRole($roleSupervisor);
    }
}
