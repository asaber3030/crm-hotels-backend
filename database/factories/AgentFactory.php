<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AgentFactory extends Factory
{

  public function definition()
  {
    return [
      'name' => $this->faker->name(),
      'email' => $this->faker->unique()->safeEmail(),
      'password' => Hash::make('123456789'),
      'contact_number' => $this->faker->phoneNumber(),
      'address' => $this->faker->address(),
      'role' => $this->faker->randomElement(['admin', 'super_admin', 'agent']),
      'state' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
