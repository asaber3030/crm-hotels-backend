<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\RoomController;
use	App\Http\Controllers\MealController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\AirportReservationController;
use App\Http\Controllers\CarReservationController;
use App\Http\Controllers\HotelReservationController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\UserController;

Route::as('api.')->prefix('v1/')->group(function () {

	Route::controller(AuthController::class)->group(function () {
		Route::post('login', 'login')->name('login');

		Route::middleware('auth:sanctum')->group(function () {
			Route::get('me', 'me');
		});
	});

	Route::middleware('auth:sanctum')->group(function () {
		Route::apiResource('cities', CityController::class);
		Route::get('/cities-trashed', [CityController::class, 'trashed']);
		Route::patch('/cities/{id}/restore', [CityController::class, 'restore']);

		Route::apiResource('hotels', HotelController::class);
		Route::get('/hotels-trashed', [HotelController::class, 'trashed']);
		Route::get('/hotels/{id}/rooms', [HotelController::class, 'hotelRooms']);
		Route::patch('/hotels/{id}/restore', [HotelController::class, 'restore']);

		Route::apiResource('agents', AgentController::class);
		Route::get('/agents-trashed', [AgentController::class, 'trashed']);
		Route::patch('/agents/{id}/restore', [AgentController::class, 'restore']);

		Route::apiResource('reservations', ReservationController::class);
		Route::get('/reservations-trashed', [ReservationController::class, 'trashed']);
		Route::controller(ReservationController::class)->prefix('reservations')->group(function () {
			Route::get('/{id}/car-reservations', 'carreservations');
			Route::get('/{id}/hotel-reservations', 'hotelReservations');
			Route::get('/{id}/airport-reservations', 'airportReservations');
		});

		Route::apiResource('clients', ClientController::class);
		Route::get('/clients-trashed', [ClientController::class, 'trashed']);
		Route::patch('/clients/{id}/restore', [ClientController::class, 'restore']);

		Route::apiResource('users', UserController::class);
		Route::get('/users-trashed', [UserController::class, 'trashed']);
		Route::patch('/users/{id}/restore', [UserController::class, 'restore']);

		Route::apiResource('drivers', DriverController::class);
		Route::get('/drivers-trashed', [DriverController::class, 'trashed']);
		Route::patch('/drivers/{id}/restore', [DriverController::class, 'restore']);

		Route::apiResource('companies', CompanyController::class);
		Route::get('/companies-trashed', [CompanyController::class, 'trashed']);
		Route::patch('/companies/{id}/restore', [CompanyController::class, 'restore']);

		Route::apiResource('rooms', RoomController::class);
		Route::get('/rooms-trashed', [RoomController::class, 'trashed']);
		Route::patch('/rooms/{id}/restore', [RoomController::class, 'restore']);

		Route::apiResource('airport-reservations', AirportReservationController::class);
		Route::get('/airport-reservations-trashed', [AirportReservationController::class, 'trashed']);
		Route::patch('/airport-reservations/{id}/restore', [AirportReservationController::class, 'restore']);

		Route::apiResource('car-reservations', CarReservationController::class);
		Route::get('/car-reservations-trashed', [CarReservationController::class, 'trashed']);
		Route::patch('/car-reservations/{id}/restore', [CarReservationController::class, 'restore']);

		Route::apiResource('hotel-reservations', HotelReservationController::class);
		Route::get('/hotel-reservations-trashed', [HotelReservationController::class, 'trashed']);
		Route::patch('/hotel-reservations/{id}/restore', [HotelReservationController::class, 'restore']);

		Route::apiResource('meals', MealController::class);
		Route::get('/meals-trashed', [MealController::class, 'trashed']);
		Route::patch('/meals/{id}/restore', [MealController::class, 'restore']);

		Route::apiResource('rates', RateController::class);
		Route::get('/rates-trashed', [RateController::class, 'trashed']);
		Route::patch('/rates/{id}/restore', [RateController::class, 'restore']);

		Route::apiResource('payment-types', PaymentTypeController::class);
		Route::get('/payment-types-trashed', [PaymentTypeController::class, 'trashed']);
		Route::patch('/payment-types/{id}/restore', [PaymentTypeController::class, 'restore']);
	});
});
