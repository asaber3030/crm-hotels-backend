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
        AirportReservation::factory(50)->create();
        CarReservation::factory(50)->create();
        City::factory(50)->create();
        Client::factory(50)->create();
        Company::factory(50)->create();
        Driver::factory(50)->create();
        Hotel::factory(50)->create();
        HotelReservation::factory(50)->create();
        Meal::factory(50)->create();
        Rate::factory(50)->create();
        Reservation::factory(50)->create();
        Room::factory(50)->create();
        Agent::factory(50)->create();
    }
}
