<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AirportReservation;

class AirportReservationController extends Controller
{
	public function index()
	{
		$reservations = AirportReservation::orderBy('id', 'desc')->paginate();
		return send_response('Airport reservations retrieved successfully', 200, $reservations);
	}

	public function store(Request $request)
	{
		$request->validate([
			'reservation_id' => 'required|exists:reservations,id',
			'airport_name' => 'required|string|max:255',
			'airline' => 'required|string|max:255',
			'runner' => 'required|string|max:255',
			'price' => 'required|numeric|min:0',
			'flight_number' => 'required|string|max:255',
			'coming_from' => 'required|string|max:255',
			'passenger_count' => 'required|string|max:255',
			'status' => 'required|string|in:pending,done,cancelled',
			'arrival_date' => 'required|date',
			'arrival_time' => 'required|date_format:H:i:s',
			'persons_count' => 'required|integer',
			'statment' => 'required|integer',
		]);

		$reservation = AirportReservation::create($request->only([
			'reservation_id',
			'airport_name',
			'airline',
			'runner',
			'price',
			'flight_number',
			'coming_from',
			'passenger_count',
			'status',
			'arrival_date',
			'arrival_time',
			'persons_count',
			'statment',
		]));

		return send_response('Airport reservation created successfully', 201, $reservation);
	}

	public function show($id)
	{
		$reservation = AirportReservation::find($id);
		if (!$reservation) {
			return send_response('Airport reservation not found', 404);
		}
		return send_response('Airport reservation retrieved successfully', 200, $reservation);
	}

	public function update(Request $request, $id)
	{
		$reservation = AirportReservation::find($id);
		if (!$reservation) {
			return send_response('Airport reservation not found', 404);
		}
		$request->validate([
			'reservation_id' => 'sometimes|exists:reservations,id',
			'airport_name' => 'sometimes|string|max:255',
			'airline' => 'sometimes|string|max:255',
			'runner' => 'sometimes|string|max:255',
			'price' => 'sometimes|numeric|min:0',
			'flight_number' => 'sometimes|string|max:255',
			'coming_from' => 'sometimes|string|max:255',
			'passenger_count' => 'sometimes|string|max:255',
			'status' => 'sometimes|string|in:pending,done,cancelled',
			'arrival_date' => 'sometimes|date',
			'arrival_time' => 'sometimes|date_format:H:i:s',
			'persons_count' => 'sometimes|integer',
			'statment' => 'sometimes|integer',
		]);

		$reservation->update($request->only([
			'reservation_id',
			'airport_name',
			'airline',
			'runner',
			'price',
			'flight_number',
			'coming_from',
			'passenger_count',
			'status',
			'arrival_date',
			'arrival_time',
			'persons_count',
			'statment',
		]));

		return send_response('Airport reservation updated successfully', 200, $reservation);
	}

	public function destroy($id)
	{
		$reservation = AirportReservation::find($id);
		if (!$reservation) {
			return send_response('Airport reservation not found', 404);
		}
		$reservation->delete();
		return send_response('Airport reservation deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedReservations = AirportReservation::onlyTrashed()->paginate();
		return send_response('Deleted airport reservations retrieved successfully', 200, $deletedReservations);
	}

	public function restore($id)
	{
		$reservation = AirportReservation::onlyTrashed()->find($id);
		if (!$reservation) {
			return send_response('Deleted airport reservation not found', 404);
		}
		$reservation->restore();
		return send_response('Airport reservation restored successfully', 200, $reservation);
	}
}
