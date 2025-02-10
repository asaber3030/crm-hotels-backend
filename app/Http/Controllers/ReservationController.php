<?php

namespace App\Http\Controllers;

use App\Models\CarReservation;
use App\Models\HotelReservation;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\AirportReservation;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{

	public function index(Request $request)
	{
		$status = $request->query('status');
		$check_in = $request->query('check_in');
		$check_out = $request->query('check_out');
		$option_date_from = $request->query('option_date_from');
		$option_date_to = $request->query('option_date_to');

		$hotel = $request->query('hotel');
		$city = $request->query('city');
		$agent = $request->query('agent');
		$client = $request->query('client');

		$query = HotelReservation::with([
			'city:id,name',
			'meal:id,meal_type',
			'rate:id,name',
			'company:id,name',
			'hotel:id,name',
			'reservation' => function ($q) {
				$q->with('client:id,name,email', 'agent:id,name,email');
			}
		]);

		if ($request->has('status')) {
			$query->whereHas('reservation', function ($q) use ($status) {
				$q->where('status', $status);
			});
		}

		if ($request->has('hotel')) {
			$query->whereHas('reservation', function ($q) use ($hotel) {
				$q->where('hotel_id', $hotel);
			});
		}

		if ($request->has('city')) {
			$query->whereHas('reservation', function ($q) use ($city) {
				$q->where('city_id', $city);
			});
		}

		if ($request->has('client')) {
			$query->whereHas('reservation', fn($q) => $q->where('client_id', $client));
		}

		if ($request->has('agent')) {
			$query->whereHas('reservation', fn($q) => $q->where('agent_id', $agent));
		}

		if ($request->has('option_date_from') && $request->has('option_date_to')) {
			$query->whereHas(
				'reservation',
				fn($q) =>
				$q->whereBetween('option_date', [$option_date_from, $option_date_to])
			);
		} elseif ($option_date_from) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('option_date', '>=', $option_date_from));
		} elseif ($option_date_to) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('option_date', '<=', $option_date_to));
		}

		if ($request->has('check_in') && $request->has('check_out')) {
			$query->whereHas(
				'reservation',
				fn($q) =>
				$q->whereBetween('check_in', [$check_in, $check_out])
					->orWhereBetween('check_out', [$check_in, $check_out])
			);
		} elseif ($request->has('check_in')) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('check_in', '=', $check_in));
		} elseif ($request->has('check_out')) {
			$query->whereHas('reservation', fn($q) => $q->whereDate('check_out', '=', $check_out));
		}

		$reservations = $query->orderBy('id', 'desc')->paginate();

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
			'airport',
			'car'
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

	public function createFullReservation(Request $request)
	{
		$request->validate([
			'client_id' => 'required|exists:clients,id',
			'agent_id' => 'required|exists:agents,id',
			'reservation_date' => 'required|date',
			'notes' => 'nullable|max:255',

			// Hotel Reservation (Optional)
			'hotel_reservation' => 'nullable|array',
			'hotel_reservation.hotel_id' => 'nullable|required_with:hotel_reservation|exists:hotels,id',
			'hotel_reservation.city_id' => 'nullable|required_with:hotel_reservation|exists:cities,id',
			'hotel_reservation.meal_id' => 'nullable|required_with:hotel_reservation|exists:meals,id',
			'hotel_reservation.company_id' => 'nullable|required_with:hotel_reservation|exists:companies,id',
			'hotel_reservation.rate_id' => 'nullable|required_with:hotel_reservation|exists:rates,id',
			'hotel_reservation.check_in' => 'nullable|required_with:hotel_reservation|date',
			'hotel_reservation.check_out' => 'nullable|required_with:hotel_reservation|date|after:hotel_reservation.check_in',
			'hotel_reservation.rooms_count' => 'nullable|required_with:hotel_reservation|integer|min:1',
			'hotel_reservation.view' => 'nullable|required_with:hotel_reservation|string|max:255',
			'hotel_reservation.pax_count' => 'nullable|required_with:hotel_reservation|integer|min:1',
			'hotel_reservation.adults' => 'nullable|required_with:hotel_reservation|integer|min:1',
			'hotel_reservation.status' => 'nullable|required_with:hotel_reservation|string|in:new,in_revision,confirmed,refunded,cancelled,guaranteed',
			'hotel_reservation.children' => 'nullable|required_with:hotel_reservation|integer|min:0',
			'hotel_reservation.option_date' => 'nullable|required_with:hotel_reservation|date',
			'hotel_reservation.confirmation_number' => 'nullable|required_with:hotel_reservation|string|max:255',
			'hotel_reservation.price' => 'nullable|required_with:hotel_reservation|numeric|min:0',

			'car_reservation' => 'nullable|array',
			'car_reservation.driver_id' => 'nullable|required_with:car_reservation|exists:drivers,id',
			'car_reservation.airline' => 'nullable|required_with:car_reservation|string|max:255',
			'car_reservation.meeting_point' => 'nullable|required_with:car_reservation|string|max:255',
			'car_reservation.arrival_date' => 'nullable|required_with:car_reservation|date',
			'car_reservation.arrival_time' => 'nullable|required_with:car_reservation|string|max:255',
			'car_reservation.coming_from' => 'nullable|required_with:car_reservation|string|max:255',
			'car_reservation.status' => 'nullable|required_with:car_reservation|string|in:pending,done,cancelled',
			'car_reservation.comments' => 'nullable|string|max:255',
			'car_reservation.price' => 'nullable|required_with:car_reservation|numeric|min:0',

			'airport_reservation' => 'nullable|array',
			'airport_reservation.airport_name' => 'nullable|required_with:airport_reservation|string|max:255',
			'airport_reservation.airline' => 'nullable|required_with:airport_reservation|string|max:255',
			'airport_reservation.runner' => 'nullable|required_with:airport_reservation|string|max:255',
			'airport_reservation.price' => 'nullable|required_with:airport_reservation|numeric|min:0',
			'airport_reservation.flight_number' => 'nullable|required_with:airport_reservation|string|max:255',
			'airport_reservation.coming_from' => 'nullable|required_with:airport_reservation|string|max:255',
			'airport_reservation.passenger_count' => 'nullable|required_with:airport_reservation|string|max:255',
			'airport_reservation.status' => 'nullable|required_with:airport_reservation|string|in:pending,done,cancelled',
			'airport_reservation.arrival_date' => 'nullable|required_with:airport_reservation|date',
			'airport_reservation.arrival_time' => 'nullable|required_with:airport_reservation|date_format:H:i:s',
			'airport_reservation.persons_count' => 'nullable|required_with:airport_reservation|integer',
			'airport_reservation.statment' => 'nullable|required_with:airport_reservation|integer',
		]);

		try {
			$reservation = Reservation::create([
				'agent_id' => $request->input('agent_id'),
				'client_id' => $request->input('client_id'),
				'reservation_date' => $request->input('reservation_date'),
				'notes' => $request->input('notes'),
			]);

			if ($request->has('hotel_reservation')) {
				HotelReservation::create([
					'reservation_id' => $reservation->id,
					'hotel_id' => $request->input('hotel_reservation.hotel_id'),
					'city_id' => $request->input('hotel_reservation.city_id'),
					'meal_id' => $request->input('hotel_reservation.meal_id'),
					'company_id' => $request->input('hotel_reservation.company_id'),
					'rate_id' => $request->input('hotel_reservation.rate_id'),
					'check_in' => $request->input('hotel_reservation.check_in'),
					'check_out' => $request->input('hotel_reservation.check_out'),
					'rooms_count' => $request->input('hotel_reservation.rooms_count'),
					'view' => $request->input('hotel_reservation.view'),
					'pax_count' => $request->input('hotel_reservation.pax_count'),
					'adults' => $request->input('hotel_reservation.adults'),
					'status' => $request->input('hotel_reservation.status'),
					'children' => $request->input('hotel_reservation.children'),
					'option_date' => $request->input('hotel_reservation.option_date'),
					'confirmation_number' => $request->input('hotel_reservation.confirmation_number'),
					'price' => $request->input('hotel_reservation.price'),
				]);
			}

			// Create car reservation if provided
			if ($request->has('car_reservation')) {
				CarReservation::create([
					'reservation_id' => $reservation->id,
					'driver_id' => $request->input('car_reservation.driver_id'),
					'airline' => $request->input('car_reservation.airline'),
					'meeting_point' => $request->input('car_reservation.meeting_point'),
					'arrival_date' => $request->input('car_reservation.arrival_date'),
					'arrival_time' => $request->input('car_reservation.arrival_time'),
					'coming_from' => $request->input('car_reservation.coming_from'),
					'status' => $request->input('car_reservation.status'),
					'comments' => $request->input('car_reservation.comments'),
					'price' => $request->input('car_reservation.price'),
				]);
			}

			// Create airport reservation if provided
			if ($request->has('airport_reservation')) {
				AirportReservation::create([
					'reservation_id' => $reservation->id,
					'airport_name' => $request->input('airport_reservation.airport_name'),
					'airline' => $request->input('airport_reservation.airline'),
					'runner' => $request->input('airport_reservation.runner'),
					'price' => $request->input('airport_reservation.price'),
					'flight_number' => $request->input('airport_reservation.flight_number'),
					'coming_from' => $request->input('airport_reservation.coming_from'),
					'passenger_count' => $request->input('airport_reservation.passenger_count'),
					'status' => $request->input('airport_reservation.status'),
					'arrival_date' => $request->input('airport_reservation.arrival_date'),
					'arrival_time' => $request->input('airport_reservation.arrival_time'),
					'persons_count' => $request->input('airport_reservation.persons_count'),
					'statment' => $request->input('airport_reservation.statment'),
				]);
			}

			DB::commit();

			return send_response('Reservation Create', 201);
		} catch (\Exception $e) {
			DB::rollBack();
			return send_response('Something went wrong.', 500);
		}
	}
}
