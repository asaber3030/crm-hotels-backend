<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarReservation;

class CarReservationController extends Controller
{
	public function index()
	{
		$carReservations = CarReservation::with(['driver', 'reservation'])->orderBy('id', 'desc')->simplePaginate();
		return send_response('Car reservations retrieved successfully', 200, $carReservations);
	}

	public function store(Request $request)
	{
		$request->validate([
			'reservation_id' => 'required|exists:reservations,id',
			'driver_id' => 'required|exists:drivers,id',
			'airline' => 'required|string|max:255',
			'meeting_point' => 'required|string|max:255',
			'arrival_date' => 'required|date',
			'arrival_time' => 'required|string|max:255',
			'coming_from' => 'required|string|max:255',
			'status' => 'required|string|in:pending,done,cancelled',
			'comments' => 'nullable|string|max:255',
			'price' => 'required|numeric|min:0',
		]);

		$carReservation = CarReservation::create($request->only([
			'reservation_id',
			'driver_id',
			'airline',
			'meeting_point',
			'arrival_date',
			'arrival_time',
			'coming_from',
			'comments',
			'price',
		]));

		return send_response('Car reservation created successfully', 201, $carReservation);
	}

	public function show($id)
	{
		$carReservation = CarReservation::with(['driver', 'reservation'])->find($id);

		if (!$carReservation) {
			return send_response('Car reservation not found', 404);
		}

		return send_response('Car reservation retrieved successfully', 200, $carReservation);
	}

	public function update(Request $request, $id)
	{
		$carReservation = CarReservation::find($id);

		if (!$carReservation) {
			return send_response('Car reservation not found', 404);
		}

		$request->validate([
			'reservation_id' => 'sometimes|exists:reservations,id',
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

		$carReservation->update($request->only([
			'reservation_id',
			'driver_id',
			'airline',
			'meeting_point',
			'arrival_date',
			'arrival_time',
			'coming_from',
			'comments',
			'price',
		]));

		return send_response('Car reservation updated successfully', 200, $carReservation);
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

	public function trashed()
	{
		$deletedCarReservations = CarReservation::onlyTrashed()->simplePaginate();
		return send_response('Deleted car reservations retrieved successfully', 200, $deletedCarReservations);
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
