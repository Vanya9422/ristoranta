<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * @var User $model
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'phone' => $this->faker->phoneNumber,
            'uid' => $this->faker->uuid,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'birthday' => $this->faker->dateTime,
            'address' => $this->faker->address
        ];
    }
}
