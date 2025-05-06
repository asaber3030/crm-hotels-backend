<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarReservation;
use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class CarReservationController extends Controller
{
	public function index(Request $request)
	{
		$search = $request->query('search');
		$status = $request->query('status');

		$reservations = CarReservation::query();

		if ($status) {
			$reservations->where('status', $status);
		}

		if ($search) {
			$reservations->where(function ($query) use ($search) {
				$query
					->orWhere('airline', 'like', "%$search%")
					->orWhere('coming_from', 'like', "%$search%")
					->orWhereHas('reservation.client', function ($q) use ($search) {
						$q->where('name', 'like', "%$search%")
							->orWhere('email', 'like', "%$search%");
					})
					->orWhereHas('driver', function ($q) use ($search) {
						$q->where('name', 'like', "%$search%");
					});
			});
		}

		$data = $reservations->with([
			'driver',
			'reservation' => fn($q) => $q->with('client')
		])->orderBy('id', 'desc')->paginate();
		return send_response('Car reservations retrieved successfully', 200, $data);
	}

	public function logs($id)
	{
		$logs = Activity::where('subject_type', CarReservation::class)
			->where('subject_id', $id)
			->with([
				'causer',
				'subject' => fn($query) => $query->with([
					'driver:id,name',
					'reservation' => fn($query) => $query
						->select('id', 'client_id')
						->with('client:id,name,email,phone,nationality'),
					'driver:id,name',
				])
			])
			->latest()
			->paginate();

		return send_response('Car reservation logs retrieved successfully', 200, $logs);
	}

	public function single_log($id, $logId)
	{
		$log = Activity::where('subject_type', CarReservation::class)
			->where('subject_id', $id)
			->with([
				'causer',
				'subject' => fn($query) => $query->with([
					'driver:id,name',
					'reservation' => fn($query) => $query
						->select('id', 'client_id')
						->with('client:id,name,email,phone,nationality'),
					'driver:id,name',
				])
			])
			->latest()
			->find($logId);

		return send_response('Car reservation log retrieved successfully', 200, $log);
	}

	public function trashed()
	{
		$deletedCarReservations = CarReservation::with([
			'driver',
			'reservation' => fn($q) => $q->with('client')
		])->orderBy('id', 'desc')->onlyTrashed()->paginate();
		return send_response('Deleted car reservations retrieved successfully', 200, $deletedCarReservations);
	}

	public function store(Request $request)
	{
		$request->validate([
			'client_id' => 'sometimes|exists:clients,id',
			'client_name' => 'required_without:client_id|string|max:255',
			'phone' => 'required_without:client_id|string|max:255|unique:clients,phone',
			'nationality' => 'required_without:client_id|string|max:255',
			'email' => 'required_without:client_id|string|max:255|unique:clients,phone',
			'driver_id' => 'required|exists:drivers,id',
			'airline' => 'required|string|max:255',
			'meeting_point' => 'required|string|max:255',
			'arrival_date' => 'required|date',
			'arrival_time' => 'required|string|max:255',
			'coming_from' => 'required|string|max:255',
			'status' => 'sometimes|string|in:pending,done,cancelled',
			'comments' => 'nullable|string|max:255',
			'price' => 'required|numeric|min:0',
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
			'driver_id',
			'airline',
			'meeting_point',
			'arrival_date',
			'arrival_time',
			'coming_from',
			'comments',
			'status',
			'price',
		]);

		$carReservation = CarReservation::create([
			...$data,
			'reservation_id' => $reservation->id,
		]);

		return send_response('Car reservation created successfully', 201, $carReservation);
	}

	public function show($id)
	{
		$carReservation = CarReservation::with([
			'driver',
			'reservation' => fn($q) => $q->with('client')
		])->find($id);

		if (!$carReservation) {
			return send_response('Car reservation not found', 404);
		}

		return send_response('Car reservation retrieved successful222ly', 200, $carReservation);
	}

	public function update(Request $request, $id)
	{
		$carReservation = CarReservation::find($id);

		if (!$carReservation) {
			return send_response('Car reservation not found', 404);
		}

		$request->validate([
			'client_id' => 'sometimes|exists:clients,id',
			'client_name' => 'required_without:client_id|string|max:255',
			'phone' => 'required_without:client_id|string|max:255|unique:clients,phone',
			'nationality' => 'required_without:client_id|string|max:255',
			'email' => 'required_without:client_id|string|max:255|unique:clients,email',
			'driver_id' => 'sometimes|exists:drivers,id',
			'airline' => 'sometimes|string|max:255',
			'meeting_point' => 'sometimes|string|max:255',
			'arrival_date' => 'sometimes|date',
			'arrival_time' => 'sometimes|string|max:255',
			'coming_from' => 'sometimes|string|max:255',
			'status' => 'sometimes|string|in:pending,done,cancelled',
			'comments' => 'nullable|string|max:255',
			'price' => 'sometimes|numeric|min:0',
		]);

		$client_id = $request->input('client_id');

		if ($client_id) {
			$client = Client::find($client_id);
			if (!$client) {
				return send_response('Client not found', 404);
			}
			$client_id = $client->id;
			Reservation::where('id', $carReservation->reservation_id)->update([
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
			Reservation::where('id', $carReservation->reservation_id)->update([
				'client_id' => $client_id,
			]);
		}

		$carReservation->update([
			'driver_id' => $request->input('driver_id'),
			'airline' => $request->input('airline'),
			'meeting_point' => $request->input('meeting_point'),
			'arrival_date' => $request->input('arrival_date'),
			'arrival_time' => $request->input('arrival_time'),
			'coming_from' => $request->input('coming_from'),
			'comments' => $request->input('comments'),
			'status' => $request->input('status'),
			'price' => $request->input('price'),
		]);

		return send_response('Car reservation updated successfully', 200, $carReservation);
	}

	public function change_status(Request $request, $id)
	{
		$carReservation = CarReservation::find($id);

		if (!$carReservation) {
			return send_response('Car reservation not found', 404);
		}

		$request->validate([
			'status' => 'sometimes|string|in:pending,done,cancelled',
		]);

		$carReservation->update($request->only([
			'status',
		]));

		return send_response('Car reservation status updated successfully', 200, $carReservation);
	}

	public function destroy($id)
	{
		$carReservation = CarReservation::find($id);

		if (!$carReservation) {
			return send_response('Car reservation not found', 404);
		}

		$carReservation->delete();
		return send_response('Car reservation deleted successfully', 200);
	}

	public function restore($id)
	{
		$carReservation = CarReservation::onlyTrashed()->find($id);

		if (!$carReservation) {
			return send_response('Deleted car reservation not found', 404);
		}

		$carReservation->restore();
		return send_response('Car reservation restored successfully', 200, $carReservation);
	}
}
