<?php

namespace Database\Seeders;

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
        AirportReservation::factory(500)->create();
        CarReservation::factory(500)->create();
        City::factory(500)->create();
        Client::factory(500)->create();
        Company::factory(500)->create();
        Driver::factory(500)->create();
        Hotel::factory(500)->create();
        HotelReservation::factory(500)->create();
        Meal::factory(500)->create();
        Rate::factory(500)->create();
        Reservation::factory(500)->create();
        Room::factory(500)->create();
        Agent::factory(500)->create();
    }
}
