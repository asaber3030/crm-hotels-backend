<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarReservationFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'reservation_id' => Reservation::inRandomOrder()->first()->id, // Randomly assign an existing reservation
      'driver_id' => Driver::inRandomOrder()->first()->id, // Randomly assign an existing driver
      'airline' => $this->faker->company(), // Generates a random airline name
      'meeting_point' => $this->faker->streetAddress(), // Generates a random meeting point
      'arrival_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'), // Random arrival date within the next year
      'arrival_time' => $this->faker->time('H:i:s'), // Random arrival time
      'coming_from' => $this->faker->city(), // Generates a random city name
      'comments' => $this->faker->sentence(), // Generates a random comment
      'status' => $this->faker->randomElement(['pending', 'done', 'cancelled']), // Random status
      'price' => $this->faker->randomFloat(2, 50, 500), // Random price between 50 and 500 with 2 decimal places
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
