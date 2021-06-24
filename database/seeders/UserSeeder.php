<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\Eloquent\User\RoleInterface;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    /**
     * @var int
     */
    protected int $userCount = 30;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(RoleInterface $role)
    {
        $roles = $role->all();

        User::factory()->count($this->userCount)->create()
            ->each(function ($user, $key) use ($roles, &$admin) {
                $roleName = !$key ? 'admin' : $roles[rand(0, (count($roles) - 1))]->name;
                $user->assignRole($roleName);
            });
    }
}

