<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->city(), // Generates a random city name
      'state' => $this->faker->randomElement(['pending', 'approved', 'rejected']), // Random state

    ];
  }
}
