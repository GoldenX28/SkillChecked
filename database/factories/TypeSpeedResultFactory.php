<?php

namespace Database\Factories;

use App\Models\TypeSpeedResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeSpeedResultFactory extends Factory
{
    protected $model = TypeSpeedResult::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'wpm' => $this->faker->numberBetween(10, 150),
            'correct_chars' => $this->faker->numberBetween(0, 2000),
            'errors' => $this->faker->numberBetween(0, 50),
            'typed_chars' => $this->faker->numberBetween(0, 2000),
            'accuracy' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}