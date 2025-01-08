<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'city_id' => City::inRandomOrder()->first()->id, // Randomly assign an existing city
      'name' => $this->faker->company(), // Generates a random company name
      'email' => $this->faker->unique()->safeEmail(), // Generates a unique email
      'phone_number' => $this->faker->phoneNumber(), // Generates a random phone number
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
