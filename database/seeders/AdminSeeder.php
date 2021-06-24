<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        User::create([
            'uid' => $faker->uuid,
            'first_name' => 'Admin_' . $faker->firstName,
            'last_name' => 'Admin_' . $faker->lastName,
            'phone' =>  $faker->phoneNumber,
            'password' => 'password',
        ])->assignRole('admin');
    }
}
