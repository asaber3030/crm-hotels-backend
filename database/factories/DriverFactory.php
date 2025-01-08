<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->name(), // Generates a random name
      'phone' => $this->faker->phoneNumber(), // Generates a random phone number
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
