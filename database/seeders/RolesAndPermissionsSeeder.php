<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roleKaryawan = \Spatie\Permission\Models\Role::create(['name' => 'karyawan']);
        $roleSupervisor = \Spatie\Permission\Models\Role::create(['name' => 'supervisor']);

        $karyawan = \App\Models\User::create([
            'name' => 'Karyawan Inspeksi',
            'email' => 'karyawan@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $karyawan->assignRole($roleKaryawan);

        $supervisor = \App\Models\User::create([
            'name' => 'Spv Produksi',
            'email' => 'spv@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $supervisor->assignRole($roleSupervisor);
    }
}
