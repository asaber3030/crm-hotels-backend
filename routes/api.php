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
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HotelReservationController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Response;

Route::as('api.')->prefix('v1/')->group(function () {

	Route::controller(AuthController::class)->group(function () {
		Route::post('login', 'login')->name('login');

		Route::middleware('auth:sanctum')->group(function () {
			Route::get('me', 'me');
		});
	});

	Route::get('/email-attachments/{filename}', function ($filename) {
		$path = public_path("email_attachments/{$filename}");
		if (!file_exists($path)) {
			abort(404);
		}
		return Response::download($path, $filename);
	});

	Route::middleware('auth:sanctum')->group(function () {

		Route::post('send-mail', [EmailController::class, 'send_email']);

		Route::apiResource('cities', CityController::class);
		Route::get('/cities-trashed', [CityController::class, 'trashed']);
		Route::get('/cities-all', [CityController::class, 'all']);
		Route::patch('/cities/{id}/restore', [CityController::class, 'restore']);

		Route::apiResource('hotels', HotelController::class);
		Route::get('/hotels-trashed', [HotelController::class, 'trashed']);
		Route::get('/hotels-all', [HotelController::class, 'all']);
		Route::get('/hotels/filter/{city}', [HotelController::class, 'filter_by_city']);
		Route::get('/hotels/{id}/rooms', [HotelController::class, 'hotelRooms']);
		Route::patch('/hotels/{id}/restore', [HotelController::class, 'restore']);

		Route::apiResource('agents', AgentController::class);
		Route::get('/agents-trashed', [AgentController::class, 'trashed']);
		Route::patch('/agents/{id}/restore', [AgentController::class, 'restore']);

		Route::apiResource('reservations', ReservationController::class);
		Route::get('/reservations-small', [ReservationController::class, 'small']);
		Route::get('/reservations-stats', [ReservationController::class, 'stats']);
		Route::get('/reservations-mine', [ReservationController::class, 'mine']);
		Route::post('/reservations/create', [ReservationController::class, 'createFullReservation']);
		Route::post('/reservations/create-with-new-client', [ReservationController::class, 'storeWithNewClient']);
		Route::patch('/reservations/{id}/update-with-new-client', [ReservationController::class, 'updateWithNewClient']);
		Route::get('/reservations/status/{status}', [ReservationController::class, 'filter_status']);
		Route::get('/reservations-option-date', [ReservationController::class, 'option_date_data']);
		Route::controller(ReservationController::class)->prefix('reservations')->group(function () {
			Route::get('/{id}/car-reservation', 'carReservation');
			Route::get('/{id}/hotel-reservation', 'hotelReservation');
			Route::get('/{id}/airport-reservation', 'airportReservation');
		});

		Route::apiResource('clients', ClientController::class);
		Route::get('/clients-trashed', [ClientController::class, 'trashed']);
		Route::get('/clients-all', [ClientController::class, 'all']);
		Route::patch('/clients/{id}/restore', [ClientController::class, 'restore']);

		Route::apiResource('users', UserController::class);
		Route::get('/users-trashed', [UserController::class, 'trashed']);
		Route::patch('/users/{id}/restore', [UserController::class, 'restore']);

		Route::apiResource('drivers', DriverController::class);
		Route::get('/drivers-trashed', [DriverController::class, 'trashed']);
		Route::get('/drivers-all', [DriverController::class, 'all']);
		Route::patch('/drivers/{id}/restore', [DriverController::class, 'restore']);

		Route::apiResource('companies', CompanyController::class);
		Route::get('/companies-trashed', [CompanyController::class, 'trashed']);
		Route::get('/companies-all', [CompanyController::class, 'all']);
		Route::patch('/companies/{id}/restore', [CompanyController::class, 'restore']);

		Route::apiResource('rooms', RoomController::class);
		Route::get('/rooms-trashed', [RoomController::class, 'trashed']);
		Route::get('/rooms-all', [RoomController::class, 'all']);
		Route::patch('/rooms/{id}/restore', [RoomController::class, 'restore']);

		Route::apiResource('airport-reservations', AirportReservationController::class);
		Route::get('/airport-reservations-trashed', [AirportReservationController::class, 'trashed']);
		Route::patch('/airport-reservations/{id}/restore', [AirportReservationController::class, 'restore']);
		Route::patch('/airport-reservations/{id}/status', [AirportReservationController::class, 'change_status']);
		Route::patch('/airport-reservations/{id}/update-with-new-client', [AirportReservationController::class, 'updateWithNewClient']);

		Route::apiResource('car-reservations', CarReservationController::class);
		Route::get('/car-reservations-trashed', [CarReservationController::class, 'trashed']);
		Route::patch('/car-reservations/{id}/restore', [CarReservationController::class, 'restore']);
		Route::patch('/car-reservations/{id}/status', [CarReservationController::class, 'change_status']);

		Route::apiResource('hotel-reservations', HotelReservationController::class);
		Route::get('/hotel-reservations-trashed', [HotelReservationController::class, 'trashed']);
		Route::get('/hotel-reservations-only', [HotelReservationController::class, 'onlyHotelReservations']);
		Route::get('/hotel-reservations-mine', [HotelReservationController::class, 'mine']);
		Route::patch('/hotel-reservations/change-many-status/status', [HotelReservationController::class, 'change_many_status']);
		Route::patch('/hotel-reservations/{id}/restore', [HotelReservationController::class, 'restore']);
		Route::patch('/hotel-reservations/{id}/change-status', [HotelReservationController::class, 'change_status']);
		Route::post('/hotel-reservations/{id}/send-voucher', [HotelReservationController::class, 'send_voucher']);

		Route::apiResource('meals', MealController::class);
		Route::get('/meals-trashed', [MealController::class, 'trashed']);
		Route::get('/meals-all', [MealController::class, 'all']);
		Route::patch('/meals/{id}/restore', [MealController::class, 'restore']);

		Route::apiResource('rates', RateController::class);
		Route::get('/rates-trashed', [RateController::class, 'trashed']);
		Route::get('/rates-all', [RateController::class, 'all']);
		Route::patch('/rates/{id}/restore', [RateController::class, 'restore']);

		Route::apiResource('vouchers', VoucherController::class);
		Route::get('/vouchers-trashed', [VoucherController::class, 'trashed']);
		Route::patch('/vouchers/{id}/restore', [VoucherController::class, 'restore']);

		Route::apiResource('payment-types', PaymentTypeController::class);
		Route::get('/payment-types-trashed', [PaymentTypeController::class, 'trashed']);
		Route::get('/payment-types-all', [PaymentTypeController::class, 'all']);
		Route::patch('/payment-types/{id}/restore', [PaymentTypeController::class, 'restore']);
	});

	Route::get('/vouchers/{id}/pdf', [VoucherController::class, 'show_pdf']);
});
