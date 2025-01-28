<?php

namespace App\Http\Controllers;

use App\Models\CarReservation;
use App\Models\HotelReservation;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\AirportReservation;

class ReservationController extends Controller
{

	public function index()
	{
		$reservations = Reservation::with(['client', 'agent'])->orderBy('id', 'desc')->paginate();
		return send_response('Reservations retrieved successfully', 200, $reservations);
	}

	public function store(Request $request)
	{
		$request->validate([
			'client_id' => 'required|integer|exists:clients,id',
			'agent_id' => 'required|integer|exists:agents,id',
			'reservation_date' => 'required|date',
			'notes' => 'nullable|string|max:500',
		]);

		$reservation = Reservation::create($request->only(['client_id', 'agent_id', 'reservation_date', 'notes']));
		return send_response('Reservation created successfully', 201, $reservation);
	}

	public function show($id)
	{
		$reservation = Reservation::with('client', 'agent')->find($id);
		if (!$reservation) {
			return send_response('Reservation not found', 404);
		}
		return send_response('Reservation retrieved successfully', 200, $reservation);
	}

	public function update(Request $request, $id)
	{
		$reservation = Reservation::find($id);

		if (!$reservation) {
			return send_response('Reservation not found', 404);
		}

		$request->validate([
			'client_id' => 'sometimes|integer|exists:clients,id',
			'agent_id' => 'sometimes|integer|exists:agents,id',
			'reservation_date' => 'sometimes|date',
			'notes' => 'nullable|string|max:500',
		]);

		$reservation->update($request->only(['client_id', 'agent_id', 'reservation_date', 'notes']));
		return send_response('Reservation updated successfully', 200, $reservation);
	}

	public function destroy($id)
	{
		$reservation = Reservation::find($id);

		if (!$reservation) {
			return send_response('Reservation not found', 404);
		}

		$reservation->delete();
		return send_response('Reservation deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedReservations = Reservation::onlyTrashed()->paginate();
		return send_response('Deleted reservations retrieved successfully', 200, $deletedReservations);
	}

	public function restore($id)
	{
		$reservation = Reservation::onlyTrashed()->find($id);

		if (!$reservation) {
			return send_response('Deleted reservation not found', 404);
		}

		$reservation->restore();
		return send_response('Reservation restored successfully', 200, $reservation);
	}

	public function carReservations($id)
	{
		$carReservations = CarReservation::with('driver')->where('reservation_id', $id)->get();
		return send_response('Car reservations retrieved successfully', 200, $carReservations);
	}

	public function hotelReservations($id)
	{
		$hotelReservations = HotelReservation::with('hotel')->where('reservation_id', $id)->get();
		return send_response('Hotel reservations retrieved successfully', 200, $hotelReservations);
	}

	public function airportReservations($id)
	{
		$airportReservations = AirportReservation::where('reservation_id', $id)->get();
		return send_response('Airport reservations retrieved successfully', 200, $airportReservations);
	}
}
