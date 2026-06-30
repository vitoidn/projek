<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roleAdmin = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $roleSupervisor = \Spatie\Permission\Models\Role::create(['name' => 'supervisor']);
        $roleOperator = \Spatie\Permission\Models\Role::create(['name' => 'operator']);

        $admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $admin->assignRole($roleAdmin);

        $supervisor = \App\Models\User::create([
            'name' => 'Spv Produksi',
            'email' => 'spv@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $supervisor->assignRole($roleSupervisor);

        $operator = \App\Models\User::create([
            'name' => 'Operator 1',
            'email' => 'operator@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $operator->assignRole($roleOperator);
    }
}
