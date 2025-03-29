<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AirportReservation;
use App\Models\Reservation;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AirportReservationController extends Controller
{
	public function index(Request $request)
	{
		$reservations = AirportReservation::query();
		$search = $request->query('search');
		$status = $request->query('status');

		if ($status) {
			$reservations->where('status', $status);
		}

		if ($search) {
			$reservations->where(function ($query) use ($search) {
				$query->where('airport_name', 'like', "%$search%")
					->orWhere('airline', 'like', "%$search%")
					->orWhere('flight_number', 'like', "%$search%")
					->orWhere('coming_from', 'like', "%$search%")
					->orWhereHas('reservation.client', function ($q) use ($search) {
						$q->where('name', 'like', "%$search%")
							->orWhere('email', 'like', "%$search%")
							->orWhere('phone', 'like', "%$search%");
					});
			});
		}

		$data = $reservations->with([
			'reservation' => fn($query) => $query->with('client'),
		])->orderBy('id', 'desc')->paginate();
		return send_response('Airport reservations retrieved successfully', 200, $data);
	}

	public function trashed()
	{
		$deletedReservations = AirportReservation::with([
			'reservation' => fn($query) => $query->with('client'),
		])->orderBy('id', 'desc')->onlyTrashed()->paginate();
		return send_response('Deleted airport reservations retrieved successfully', 200, $deletedReservations);
	}

	public function change_status(Request $request, $id)
	{
		$airportReservation = AirportReservation::find($id);

		if (!$airportReservation) {
			return send_response('Airport reservation not found', 404);
		}

		$request->validate([
			'status' => 'sometimes|string|in:pending,done,cancelled',
		]);

		$airportReservation->update($request->only([
			'status',
		]));

		return send_response('Airport reservation status updated successfully', 200, $airportReservation);
	}

	public function store(Request $request)
	{
		$request->validate([
			'client_id' => 'sometimes|exists:clients,id',
			'client_name' => 'required_without:client_id|string|max:255',
			'phone' => 'required_without:client_id|string|max:255|unique:clients,phone',
			'nationality' => 'sometimes|string|max:255',
			'email' => 'sometimes|string|max:255|unique:clients,phone',
			'airport_name' => 'required|string|max:255',
			'airline' => 'required|string|max:255',
			'runner' => 'required|string|max:255',
			'price' => 'required|numeric|min:0',
			'flight_number' => 'required|string|max:255',
			'coming_from' => 'required|string|max:255',
			'passenger_count' => 'required|integer|max:255',
			'status' => 'required|string|in:pending,done,cancelled',
			'arrival_date' => 'required|date',
			'arrival_time' => 'required|string',
			'persons_count' => 'required|integer',
			'statment' => 'required|integer',
		]);

		$client_id = $request->input('client_id');

		if ($request->input('client_id')) {
			$client = Client::find($request->input('client_id'));
			if (!$client) {
				return send_response('Client not found', 404);
			}
			$client_id = $client->id;
		} else {
			$client = Client::create([
				'name' => $request->input('client_name'),
				'email' => $request->input('email'),
				'nationality' => $request->input('nationality'),
				'phone' =>  $request->input('phone'),
			]);
			$client_id = $client->id;
		}

		$agent = Auth::user()->id;
		$reservation = Reservation::create([
			'agent_id' => $agent,
			'reservation_date' => Carbon::now(),
			'client_id' => $client_id,
		]);

		$data = $request->only([
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
		]);

		$data['reservation_id'] = $reservation->id;
		$reservation = AirportReservation::create($data);

		return send_response('Airport Reservation created successfully', 201, $reservation);
	}

	public function show($id)
	{
		$reservation = AirportReservation::with(['reservation' => fn($q) => $q->with('client')])->find($id);
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
			'client_id' => 'required_without:client_name|exists:clients,id',
			'client_name' => 'required_without:client_id|string|max:255',
			'phone' => 'required_without:client_id|string|max:255|unique:clients,phone',
			'nationality' => 'required_without:client_id|string|max:255',
			'email' => 'sometimes|string|max:255|unique:clients,email',
			'airport_name' => 'sometimes|string|max:255',
			'airline' => 'sometimes|string|max:255',
			'runner' => 'sometimes|string|max:255',
			'price' => 'sometimes|numeric|min:0',
			'flight_number' => 'sometimes|string|max:255',
			'coming_from' => 'sometimes|string|max:255',
			'passenger_count' => 'sometimes|numeric|max:255',
			'status' => 'sometimes|string|in:pending,done,cancelled',
			'arrival_date' => 'sometimes|date',
			'arrival_time' => 'sometimes|string',
			'persons_count' => 'sometimes|integer',
			'statment' => 'sometimes|integer',
		]);

		$client_id = $request->input('client_id');

		if ($client_id) {
			$client = Client::find($client_id);
			if (!$client) {
				return send_response('Client not found', 404);
			}
			$client_id = $client->id;
			Reservation::where('id', $reservation->reservation_id)->update([
				'client_id' => $client_id,
			]);
		}

		if ($request->input('client_name') && $request->input('phone') && $request->input('nationality')) {
			$client = Client::create([
				'name' => $request->input('client_name'),
				'email' => $request->input('email'),
				'nationality' => $request->input('nationality'),
				'phone' =>  $request->input('phone'),
			]);
			$client_id = $client->id;
			Reservation::where('id', $reservation->reservation_id)->update([
				'client_id' => $client_id,
			]);
		}

		$reservation->update($request->only([
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

	public function updateWithNewClient(Request $request, $id)
	{
		$reservation = AirportReservation::find($id);

		if (!$reservation) {
			return send_response('Airport reservation not found', 404);
		}

		$request->validate([
			'client_name' => 'required|string|max:255',
			'phone' => 'required|string|max:255|unique:clients,phone',
			'nationality' => 'required|string|max:255',
			'email' => 'sometimes|string|max:255|unique:clients,email',
			'airport_name' => 'sometimes|string|max:255',
			'airline' => 'sometimes|string|max:255',
			'runner' => 'sometimes|string|max:255',
			'price' => 'sometimes|numeric|min:0',
			'flight_number' => 'sometimes|string|max:255',
			'coming_from' => 'sometimes|string|max:255',
			'passenger_count' => 'sometimes|numeric|max:255',
			'status' => 'sometimes|string|in:pending,done,cancelled',
			'arrival_date' => 'sometimes|date',
			'arrival_time' => 'sometimes|string',
			'persons_count' => 'sometimes|integer',
			'statment' => 'sometimes|integer',
		]);

		$client = Client::create([
			'name' => $request->input('client_name'),
			'email' => $request->input('email'),
			'nationality' => $request->input('nationality'),
			'phone' =>  $request->input('phone'),
		]);

		Reservation::where('id', $reservation->reservation_id)->update([
			'client_id' => $client->id,
		]);

		$reservation->update($request->only([
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
