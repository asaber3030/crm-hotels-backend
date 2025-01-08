<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->company(),
      'state' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
