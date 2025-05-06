<?php

namespace App\Http\Controllers;

use App\Models\AirportReservation;
use App\Models\CarReservation;
use Illuminate\Http\Request;
use App\Models\HotelReservation;
use Spatie\Activitylog\Models\Activity;

class HistoryController extends Controller
{
	public function hotel_reservations_history(Request $request)
	{
		$orderBy = $request->query('orderBy', 'id');
		$orderType = $request->query('orderType', 'desc');

		$logs = Activity::where('subject_type', HotelReservation::class)
			->with([
				'causer',
				'subject' => fn($query) => $query->with([
					'hotel:id,name',
					'meal:id,meal_type',
					'city:id,name',
					'company:id,name',
					'reservation' => fn($query) => $query
						->select('id', 'client_id')
						->with('client:id,name,email,phone,nationality'),
					'room:id,room_type'
				])
			])
			->orderBy($orderBy, $orderType)
			->latest()
			->paginate();

		return send_response('Hotel reservation log retrieved successfully', 200, $logs);
	}

	public function car_reservations_history(Request $request)
	{
		$orderBy = $request->query('orderBy', 'id');
		$orderType = $request->query('orderType', 'desc');

		$logs = Activity::where('subject_type', CarReservation::class)
			->with([
				'causer',
				'subject' => fn($query) => $query->with([
					'reservation' => fn($query) => $query
						->select('id', 'client_id')
						->with('client:id,name,email,phone,nationality'),
					'driver:id,name',
				])
			])
			->orderBy($orderBy, $orderType)
			->latest()
			->paginate();

		return send_response('Car reservations log retrieved successfully', 200, $logs);
	}

	public function airport_reservations_history(Request $request)
	{
		$orderBy = $request->query('orderBy', 'id');
		$orderType = $request->query('orderType', 'desc');

		$logs = Activity::where('subject_type', AirportReservation::class)
			->with([
				'causer',
				'subject' => fn($query) => $query->with([
					'reservation' => fn($query) => $query
						->select('id', 'client_id')
						->with('client:id,name,email,phone,nationality'),

				])
			])
			->orderBy($orderBy, $orderType)
			->latest()
			->paginate();

		return send_response('Airport reservations log retrieved successfully', 200, $logs);
	}
}
