<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'meal_type' => $this->faker->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snack']), // Random meal type
      'state' => $this->faker->randomElement(['pending', 'approved', 'rejected']), // Random state
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
