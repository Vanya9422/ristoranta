<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(
            config('roles')
        )->each(function ($item, $key) {
            $role = Role::firstOrNew(['name' => $key]);
            if (!$role->exists) {
                $role->fill([
                    'display_name' => __($item['display_name']),
                    'guard_name' => config('auth.defaults.guard'),
                ])->save();
            }
        });
    }
}
