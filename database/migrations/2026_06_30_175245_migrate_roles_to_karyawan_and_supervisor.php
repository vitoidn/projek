<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesTable = config('permission.table_names.roles', 'roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');

        $adminRole = DB::table($rolesTable)->where('name', 'admin')->first();
        $operatorRole = DB::table($rolesTable)->where('name', 'operator')->first();
        $supervisorRole = DB::table($rolesTable)->where('name', 'supervisor')->first();

        $karyawanRole = DB::table($rolesTable)->where('name', 'karyawan')->first();
        if (!$karyawanRole) {
            $karyawanRoleId = DB::table($rolesTable)->insertGetId([
                'name' => 'karyawan',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $karyawanRoleId = $karyawanRole->id;
        }

        // Migrate operator users to karyawan
        if ($operatorRole) {
            DB::table($modelHasRolesTable)
                ->where('role_id', $operatorRole->id)
                ->where('model_type', (new \App\Models\User)->getMorphClass())
                ->update(['role_id' => $karyawanRoleId]);

            DB::table($rolesTable)->where('id', $operatorRole->id)->delete();
        }

        // Migrate admin users to supervisor (if not already supervisor)
        if ($adminRole && $supervisorRole) {
            $adminUserIds = DB::table($modelHasRolesTable)
                ->where('role_id', $adminRole->id)
                ->where('model_type', (new \App\Models\User)->getMorphClass())
                ->pluck('model_id');

            foreach ($adminUserIds as $userId) {
                $hasSupervisor = DB::table($modelHasRolesTable)
                    ->where('role_id', $supervisorRole->id)
                    ->where('model_id', $userId)
                    ->where('model_type', (new \App\Models\User)->getMorphClass())
                    ->exists();

                if (!$hasSupervisor) {
                    DB::table($modelHasRolesTable)->insert([
                        'role_id' => $supervisorRole->id,
                        'model_type' => (new \App\Models\User)->getMorphClass(),
                        'model_id' => $userId,
                    ]);
                }
            }

            DB::table($modelHasRolesTable)
                ->where('role_id', $adminRole->id)
                ->where('model_type', (new \App\Models\User)->getMorphClass())
                ->delete();

            DB::table($rolesTable)->where('id', $adminRole->id)->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesTable = config('permission.table_names.roles', 'roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');

        $karyawanRole = DB::table($rolesTable)->where('name', 'karyawan')->first();
        $operatorRoleId = DB::table($rolesTable)->insertGetId([
            'name' => 'operator', 'guard_name' => 'web',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $adminRoleId = DB::table($rolesTable)->insertGetId([
            'name' => 'admin', 'guard_name' => 'web',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Revert karyawan → operator
        if ($karyawanRole) {
            DB::table($modelHasRolesTable)
                ->where('role_id', $karyawanRole->id)
                ->where('model_type', (new \App\Models\User)->getMorphClass())
                ->update(['role_id' => $operatorRoleId]);
            DB::table($rolesTable)->where('id', $karyawanRole->id)->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
