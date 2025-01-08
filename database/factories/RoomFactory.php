<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'room_type' => $this->faker->randomElement(['Single', 'Double', 'Suite', 'Deluxe']),
      'hotel_id' => Hotel::inRandomOrder()->first()->id,
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
