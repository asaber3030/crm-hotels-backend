<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Hotel;
use App\Models\City;
use App\Models\Meal;
use App\Models\Company;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelReservationFactory extends Factory
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
      'hotel_id' => Hotel::inRandomOrder()->first()->id, // Randomly assign an existing hotel
      'city_id' => City::inRandomOrder()->first()->id, // Randomly assign an existing city
      'meal_id' => Meal::inRandomOrder()->first()->id, // Randomly assign an existing meal
      'company_id' => Company::inRandomOrder()->first()->id, // Randomly assign an existing company
      'rate_id' => Rate::inRandomOrder()->first()->id, // Randomly assign an existing rate
      'check_in' => $this->faker->dateTimeBetween('now', '+3 year')->format('Y-m-d'), // Random check-in date within the next year
      'check_out' => $this->faker->dateTimeBetween('+1 day', '+3 year')->format('Y-m-d'), // Random check-out date after check-in
      'rooms_count' => $this->faker->numberBetween(1, 5), // Random number of rooms (1 to 5)
      'view' => $this->faker->randomElement(['Sea View', 'Mountain View', 'City View', 'Garden View']), // Random view type
      'pax_count' => $this->faker->numberBetween(1, 10), // Random pax count (1 to 10)
      'adults' => $this->faker->numberBetween(1, 5), // Random number of adults (1 to 5)
      'children' => $this->faker->numberBetween(0, 5), // Random number of children (0 to 5)
      'option_date' => $this->faker->dateTimeBetween('now', '+3 year')->format('Y-m-d'), // Random option date within the next year
      'confirmation_number' => $this->faker->bothify('??####'), // Random confirmation number (e.g., AB1234)
      'price' => $this->faker->randomFloat(2, 100, 1000), // Random price between 100 and 1000 with 2 decimal places
      'status' => $this->faker->randomElement(['new', 'in_revision', 'confirmed', 'refunded', 'cancelled', 'guaranteed']), // Random status
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
