<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AirportReservation;
use App\Models\CarReservation;
use App\Models\City;
use App\Models\Client;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Meal;
use App\Models\Rate;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Agent;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AirportReservation::factory(10)->create();
        CarReservation::factory(10)->create();
        City::factory(10)->create();
        Client::factory(10)->create();
        Company::factory(10)->create();
        Driver::factory(10)->create();
        Hotel::factory(10)->create();
        HotelReservation::factory(10)->create();
        Meal::factory(10)->create();
        Rate::factory(10)->create();
        Reservation::factory(10)->create();
        Room::factory(10)->create();
        Agent::factory(10)->create();
    }
}
