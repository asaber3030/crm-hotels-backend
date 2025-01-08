<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AirportReservationFactory extends Factory
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
      'airport_name' => $this->faker->city() . ' Airport', // Generates a random airport name
      'airline' => $this->faker->company(), // Generates a random airline name
      'runner' => $this->faker->name(), // Generates a random runner name
      'price' => $this->faker->numberBetween(100, 1000), // Random price between 100 and 1000
      'flight_number' => $this->faker->bothify('??###'), // Generates a random flight number (e.g., AB123)
      'coming_from' => $this->faker->city(), // Generates a random city name
      'passenger_count' => $this->faker->numberBetween(1, 10), // Random passenger count between 1 and 10
      'status' => $this->faker->randomElement(['pending', 'done', 'cancelled']), // Random status
      'arrival_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'), // Random arrival date within the next year
      'arrival_time' => $this->faker->time('H:i:s'), // Random arrival time
      'persons_count' => $this->faker->numberBetween(1, 10), // Random persons count between 1 and 10
      'statment' => $this->faker->numberBetween(0, 1), // Random statement (0 or 1)
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
