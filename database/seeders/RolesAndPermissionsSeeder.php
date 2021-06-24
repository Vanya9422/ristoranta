<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::all()->map(function ($role) {
            $permissions = config('roles')[$role->name]['permissions'];
            if (count($permissions)){
                collect($permissions)->map(function ($permission) use ($role) {
                    Permission::firstOrCreate(['name' => $permission]);
                    $role->givePermissionTo($permission);
                });
            }
        });
    }
}
