<?php

namespace App\Http\Controllers;

use App\Models\CarReservation;
use App\Models\HotelReservation;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\AirportReservation;

class ReservationController extends Controller
{

	public function index(Request $request)
	{
		$status = $request->query('status');
		$check_in = $request->query('check_in');
		$check_out = $request->query('check_out');

		$query = Reservation::with([
			'client:id,name',
			'agent:id,name',
			'reservation' => fn($q) => $q->select('id', 'reservation_id', 'check_in', 'check_out', 'city_id', 'meal_id', 'rate_id', 'company_id', 'hotel_id', 'rooms_count', 'option_date', 'price', 'pax_count', 'status')->with([
				'city:id,name',
				'meal:id,meal_type',
				'rate:id,name',
				'company:id,name',
				'hotel:id,name',
			]),
			'airport:id,reservation_id,airport_name',
			'car:id,reservation_id,airline',
		]);

		if (isset($status)) {
			$query->whereHas('reservation', fn($q) => $q->where('status', $status));
		}

		if ($check_in && $check_out) {
			$query->whereHas(
				'reservation',
				fn($q) =>
				$q->whereBetween('check_in', [$check_in, $check_out])
					->orWhereBetween('check_out', [$check_in, $check_out])
			);
		} elseif ($check_in) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('check_in', '>=', $check_in));
		} elseif ($check_out) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('check_out', '<=', $check_out));
		}

		$reservations = $query->orderBy('id', 'desc')->simplePaginate();

		return send_response('Reservations retrieved successfully', 200, $reservations);
	}

	public function option_date_data(Request $request)
	{

		$option_date_from = $request->query('option_date_from');
		$option_date_to = $request->query('option_date_to');

		$query = Reservation::with([
			'client:id,name',
			'agent:id,name',
			'reservation' => fn($q) => $q->select('id', 'reservation_id', 'check_in', 'check_out', 'city_id', 'meal_id', 'rate_id', 'company_id', 'hotel_id', 'rooms_count', 'option_date', 'price', 'pax_count', 'status')->with([
				'city:id,name',
				'meal:id,meal_type',
				'rate:id,name',
				'company:id,name',
				'hotel:id,name',
			]),
			'airport:id,reservation_id,airport_name',
			'car:id,reservation_id,airline',
		]);

		if ($option_date_from && $option_date_to) {
			$query->whereHas(
				'reservation',
				fn($q) =>
				$q->whereBetween('option_date', [$option_date_from, $option_date_to])
			);
		}
		$reservations = $query->orderBy('id', 'desc')->simplePaginate();
		return send_response('Reservations retrieved successfully', 200, $reservations);
	}

	public function filter_status(Request $request, string $status)
	{

		$check_in = $request->query('check_in');
		$check_out = $request->query('check_out');

		$query = Reservation::with([
			'client:id,name',
			'agent:id,name',
			'reservation' => fn($q) => $q->select('id', 'reservation_id', 'check_in', 'check_out', 'city_id', 'meal_id', 'rate_id', 'company_id', 'hotel_id', 'rooms_count', 'option_date', 'price', 'pax_count', 'status')->with([
				'city:id,name',
				'meal:id,meal_type',
				'rate:id,name',
				'company:id,name',
				'hotel:id,name',
			]),
			'airport:id,reservation_id,airport_name',
			'car:id,reservation_id,airline',
		]);

		$query->whereHas('reservation', fn($q) => $q->where('status', $status));

		if ($check_in && $check_out) {
			$query->whereHas(
				'reservation',
				fn($q) =>
				$q->whereBetween('check_in', [$check_in, $check_out])
					->orWhereBetween('check_out', [$check_in, $check_out])
			);
		} elseif ($check_in) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('check_in', '>=', $check_in));
		} elseif ($check_out) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('check_out', '<=', $check_out));
		}

		$reservations = $query->orderBy('id', 'desc')->simplePaginate();

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
		$reservation = Reservation::with([
			'client:id,name',
			'agent:id,name',
			'reservation' => fn($q) => $q->select('id', 'reservation_id', 'check_in', 'check_out', 'city_id', 'meal_id', 'rate_id', 'company_id', 'hotel_id', 'rooms_count', 'option_date', 'price', 'pax_count', 'status')->with([
				'city:id,name',
				'meal:id,meal_type',
				'rate:id,name',
				'company:id,name',
				'hotel:id,name',
			]),

		])->find($id);
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
		$deletedReservations = Reservation::onlyTrashed()->simplePaginate();
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

	public function carReservation($id)
	{
		$carReservations = CarReservation::with('driver')->where('reservation_id', $id)->get()->first();
		return send_response('Car reservations retrieved successfully', 200, $carReservations);
	}

	public function hotelReservation($id)
	{
		$hotelReservations = HotelReservation::with(['hotel', 'meal', 'rate', 'company', 'city'])->where('reservation_id', $id)->get()->first();
		return send_response('Hotel reservations retrieved successfully', 200, $hotelReservations);
	}

	public function airportReservation($id)
	{
		$airportReservations = AirportReservation::where('reservation_id', $id)->get()->first();
		return send_response('Airport reservations retrieved successfully', 200, $airportReservations);
	}
}
