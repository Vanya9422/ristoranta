<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'business_id' => rand(1, 100),
            'status' => 'open',
            'seats' => rand(1, 100),
            'number' => rand(1, 100),
        ];
    }
}
