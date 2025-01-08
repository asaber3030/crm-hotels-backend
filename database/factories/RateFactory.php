<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RateFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->word(), // Generates a random word for the rate name
      'state' => $this->faker->randomElement(['pending', 'approved', 'rejected']), // Random state
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
