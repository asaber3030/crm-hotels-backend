<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'client_id' => \App\Models\Client::factory(), // Randomly assign an existing client
      'agent_id' => \App\Models\Agent::factory(), // Randomly assign an existing agent
      'reservation_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'), // Random reservation date
      'notes' => $this->faker->sentence(), // Random notes
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
