<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CarReservation;
use App\Models\HotelReservation;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\AirportReservation;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateReservationRequest;
use Illuminate\Support\Facades\Auth;

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

	public function small(Request $request)
	{
		$reservations = HotelReservation::query();
		$take = $request->query('take') ?? 6;

		if ($request->query('status')) {
			$reservations->where('status', $request->query('status'));
		}

		$data = $reservations
			->select('id', 'check_in', 'check_out', 'reservation_id')
			->with([
				'reservation' => function ($q) {
					$q
						->where('deleted_at', null)
						->select('id', 'client_id', 'agent_id')
						->with(['client:id,name', 'agent:id,name']);
				},
			])
			->orderBy('id', 'desc')
			->take($take)
			->get();
		return send_response('Reservations retrieved successfully', 200, $data);
	}

	public function show($id)
	{
		$reservation = Reservation::with([
			'client',
			'agent',
			'hotel' => fn($q) => $q->with([
				'city',
				'meal',
				'rate',
				'room',
				'payment_type',
				'company',
				'hotel',
				'reservation'
			]),
			'airport',
			'car' => fn($q) => $q->with('driver')
		])->find($id);
		if (!$reservation) {
			return send_response('Reservation not found', 404);
		}
		return send_response('Reservation retrieved successfully', 200, $reservation);
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

	public function store(CreateReservationRequest $request)
	{

		DB::beginTransaction();

		try {

			$client = $request->input('client.client_id');

			$reservation = Reservation::create([
				'client_id' => $client,
				'agent_id' => Auth::id(),
				'reservation_date' => now(),
				'notes' => $request->input('hotel.notes', null),
			]);


			if ($request->has('hotel')) {
				$hotelData = [
					'hotel_id' => $request->input('hotel.hotel_id'),
					'city_id' => $request->input('hotel.city_id'),
					'meal_id' => $request->input('hotel.meal_id'),
					'company_id' => $request->input('hotel.company_id'),
					'payment_type_id' => $request->input('hotel.payment_type_id'),
					'rate_id' => $request->input('hotel.rate_id'),
					'check_in' => \Carbon\Carbon::parse($request->input('hotel.check_in'))->format('Y-m-d'),
					'check_out' => \Carbon\Carbon::parse($request->input('hotel.check_out'))->format('Y-m-d'),
					'rooms_count' => $request->input('hotel.rooms_count'),
					'price_type' => $request->input('hotel.price_type'),
					'view' => $request->input('hotel.view'),
					'pax_count' => $request->input('hotel.pax_count'),
					'status' => $request->input('hotel.status'),
					'adults' => $request->input('hotel.adults'),
					'children' => $request->input('hotel.children'),
					'option_date' => \Carbon\Carbon::parse($request->input('hotel.option_date'))->format('Y-m-d'),
					'confirmation_number' => $request->input('hotel.confirmation_number'),
					'price' => $request->input('hotel.price', 0) ? $request->input('hotel.price') : 0,
				];
				$hotelData['reservation_id'] = $reservation->id;
				$res = HotelReservation::create($hotelData);

				if ($request->input('hotel.price_type') === 'static') {
					$price = $request->input('hotel.price');
					$res->update(['price' => $price]);
				} elseif ($request->input('hotel.price_type') === 'dynamic') {
					$priceList = $request->input('hotel.price_list');
					foreach ($priceList as $priceData) {
						$res->prices()->create([
							'hotel_reservation_id' => $res->id,
							'day_number' => $priceData['day_number'],
							'price' => $priceData['price'],
						]);
					}
					$price = $res->prices()->sum('price');
					$res->update(['price' => $price]);
				}
			}

			if ($request->has('car')) {
				$carData = $request->input('car');
				$carData['reservation_id'] = $reservation->id;
				CarReservation::create($carData);
			}

			if ($request->has('airport')) {
				$airportData = $request->input('airport');
				$airportData['reservation_id'] = $reservation->id;
				AirportReservation::create($airportData);
			}

			DB::commit();

			return send_response(
				'Reservation created successfully',
				201,
				$reservation->load(['client', 'car', 'reservation', 'airport'])
			);
		} catch (\Exception $e) {
			DB::rollBack();
			return send_response('Failed to create reservation', 500, ['errors' => $e->getMessage()]);
		}
	}

	public function storeWithNewClient(CreateReservationRequest $request)
	{

		DB::beginTransaction();

		try {

			$client = Client::create(
				[
					'email' => $request->input('client.email'),
					'name' => $request->input('client.name'),
					'phone' => $request->input('client.phone'),
					'nationality' => $request->input('client.nationality'),
				]
			);

			$reservation = Reservation::create([
				'client_id' => $client->id,
				'agent_id' => Auth::id(),
				'reservation_date' => now(),
				'notes' => $request->input('hotel.notes', null),
			]);


			if ($request->has('hotel')) {
				$hotelData = $request->input('hotel');
				$hotelData['reservation_id'] = $reservation->id;
				HotelReservation::create($hotelData);
			}

			if ($request->has('car')) {
				$carData = $request->input('car');
				$carData['reservation_id'] = $reservation->id;
				CarReservation::create($carData);
			}

			if ($request->has('airport')) {
				$airportData = $request->input('airport');
				$airportData['reservation_id'] = $reservation->id;
				AirportReservation::create($airportData);
			}

			DB::commit();

			return send_response(
				'Reservation created successfully',
				201,
				$reservation->load(['client', 'car', 'reservation', 'airport'])
			);
		} catch (\Exception $e) {
			DB::rollBack();
			return send_response('Failed to create reservation', 500, ['error' => $e->getMessage()]);
		}
	}

	public function update(CreateReservationRequest $request, $id)
	{

		DB::beginTransaction();

		try {

			$client = $request->input('client.client_id');

			$reservation = Reservation::find($id);
			$reservation->update([
				'client_id' => $client,
			]);

			if ($request->has('hotel')) {
				$hotelData = $request->input('hotel');
				$hotelData['reservation_id'] = $reservation->id;
				HotelReservation::updateOrCreate([
					'reservation_id' => $reservation->id
				], $hotelData);
			}

			if ($request->filled('car')) {
				$carData = $request->input('car');
				$carData['reservation_id'] = $reservation->id;
				CarReservation::updateOrCreate(
					['reservation_id' => $reservation->id],
					$carData
				);
			} else {
				CarReservation::where('reservation_id', $reservation->id)->delete();
			}

			if ($request->filled('airport')) {
				$airportData = $request->input('airport');
				$airportData['reservation_id'] = $reservation->id;
				AirportReservation::updateOrCreate(
					['reservation_id' => $reservation->id],
					$airportData
				);
			} else {
				AirportReservation::where('reservation_id', $reservation->id)->delete();
			}

			DB::commit();

			return send_response('Reservation Updated successfully', 201, $reservation);
		} catch (\Exception $e) {
			DB::rollBack();
			return send_response('Failed to update reservation', 500, ['errors' => $e->getMessage()]);
		}
	}

	public function updateWithNewClient(CreateReservationRequest $request, $id)
	{

		DB::beginTransaction();

		try {

			$client = Client::create([
				'email' => $request->input('client.email'),
				'name' => $request->input('client.name'),
				'phone' => $request->input('client.phone'),
				'nationality' => $request->input('client.nationality'),
			]);

			$reservation = Reservation::where('id', $id)->update([
				'client_id' => $client->id
			]);

			if ($request->has('hotel')) {
				$hotelData = $request->input('hotel');
				$hotelData['reservation_id'] = $id;
				HotelReservation::updateOrCreate([
					'reservation_id' => $id
				], $hotelData);
			}


			if ($request->filled('car')) {
				$carData = $request->input('car');
				$carData['reservation_id'] = $id;
				CarReservation::updateOrCreate(
					['reservation_id' => $id],
					$carData
				);
			} else {
				CarReservation::where('reservation_id', $id)->delete();
			}

			if ($request->filled('airport')) {
				$airportData = $request->input('airport');
				$airportData['reservation_id'] = $id;
				AirportReservation::updateOrCreate(
					['reservation_id' => $id],
					$airportData
				);
			} else {
				AirportReservation::where('reservation_id', $id)->delete();
			}

			DB::commit();

			return send_response('Reservation update successfully', 201);
		} catch (\Exception $e) {
			DB::rollBack();
			return send_response('Failed to update reservation', 500, ['error' => $e->getMessage()]);
		}
	}

	public function stats()
	{
		$countRoomsToday = HotelReservation::whereDate('check_in', today())->count();
		$countRoomsTomorrow = HotelReservation::whereDate('check_in', today()->addDay())->count();

		$countAirportToday = AirportReservation::whereDate('arrival_date', today())->count();
		$countAirportTomorrow = AirportReservation::whereDate('arrival_date', today()->addDay())->count();

		$reservations = [
			'new' => HotelReservation::where([
				'status' => 'new',
				'check_in' => today()
			])->count(),
			'done' => HotelReservation::where([
				'status' => 'done',
				'check_in' => today()
			])->count(),
			'cancelled' => HotelReservation::where([
				'status' => 'cancelled',
				'check_in' => today()
			])->count(),
			'in_revision' => HotelReservation::where([
				'status' => 'in_revision',
				'check_in' => today()
			])->count(),
		];

		$countCarToday = CarReservation::whereDate('arrival_date', today())->count();
		$countCarTomorrow = CarReservation::whereDate('arrival_date', today()->addDay())->count();

		return send_response('Stats Data', 200, [
			'rooms' => [
				'today' => $countRoomsToday,
				'tomorrow' => $countRoomsTomorrow
			],
			'airport' => [
				'today' => $countAirportToday,
				'tomorrow' => $countAirportTomorrow
			],
			'car' => [
				'today' => $countCarToday,
				'tomorrow' => $countCarTomorrow
			],
			'reservations' => $reservations
		]);
	}
}
