<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
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
      'email' => $this->faker->unique()->safeEmail(), // Generates a unique email
      'nationality' => $this->faker->country(), // Generates a random country name
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
