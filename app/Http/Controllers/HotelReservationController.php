<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelReservation;
use Illuminate\Support\Facades\Auth;

class HotelReservationController extends Controller
{
	public function index(Request $request)
	{
		$query = HotelReservation::with([
			'rate',
			'hotel',
			'meal',
			'city',
			'company',
			'room',
			'reservation' => fn($query) => $query->with('client')
		]);

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		if ($request->filled('check_in')) {
			$query->whereDate('check_in', $request->check_in);
		}

		if ($request->filled('check_out')) {
			$query->whereDate('check_out', $request->check_out);
		}

		if ($request->filled('option_date_from')) {
			$query->whereDate('option_date', '>=', $request->option_date_from);
		}

		if ($request->filled('option_date_to')) {
			$query->whereDate('option_date', '<=', $request->option_date_to);
		}

		if ($request->filled('company_id')) {
			$query->where('company_id', $request->company_id);
		}

		if ($request->filled('hotel_id')) {
			$query->where('hotel_id', $request->hotel_id);
		}

		$hotelReservations = $query->orderBy('id', 'desc')->paginate();

		return send_response('Hotel reservations retrieved successfully', 200, $hotelReservations);
	}

	public function mine(Request $request)
	{
		$query = HotelReservation::with([
			'rate',
			'hotel',
			'meal',
			'city',
			'company',
			'room',
			'reservation' => fn($query) => $query->with('client')
		]);

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		if ($request->filled('check_in')) {
			$query->whereDate('check_in', $request->check_in);
		}

		if ($request->filled('check_out')) {
			$query->whereDate('check_out', $request->check_out);
		}

		if ($request->filled('option_date_from')) {
			$query->whereDate('option_date', '>=', $request->option_date_from);
		}

		if ($request->filled('option_date_to')) {
			$query->whereDate('option_date', '<=', $request->option_date_to);
		}

		if ($request->filled('company_id')) {
			$query->where('company_id', $request->company_id);
		}

		if ($request->filled('hotel_id')) {
			$query->where('hotel_id', $request->hotel_id);
		}

		$query->whereHas('reservation', fn($query) => $query->where('agent_id', Auth::user()->id));
		$hotelReservations = $query->orderBy('id', 'desc')->paginate();

		return send_response('Hotel reservations retrieved successfully', 200, $hotelReservations);
	}

	public function onlyHotelReservations(Request $request)
	{
		$query = HotelReservation::with([
			'rate',
			'hotel',
			'meal',
			'city',
			'company',
			'room',
			'reservation' => fn($query) =>
			$query->whereDoesntHave('airport')
				->whereDoesntHave('car')
				->with('client')
		]);

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		if ($request->filled('check_in')) {
			$query->whereDate('check_in', $request->check_in);
		}

		if ($request->filled('check_out')) {
			$query->whereDate('check_out', $request->check_out);
		}

		if ($request->filled('option_date_from')) {
			$query->whereDate('option_date', '>=', $request->option_date_from);
		}

		if ($request->filled('option_date_to')) {
			$query->whereDate('option_date', '<=', $request->option_date_to);
		}

		if ($request->filled('company_id')) {
			$query->where('company_id', $request->company_id);
		}

		if ($request->filled('hotel_id')) {
			$query->where('hotel_id', $request->hotel_id);
		}

		$query->whereHas('reservation', fn($query) => $query->where('agent_id', Auth::user()->id));
		$hotelReservations = $query->orderBy('id', 'desc')->paginate();

		return send_response('Hotel reservations retrieved successfully', 200, $hotelReservations);
	}

	public function store(Request $request)
	{
		$request->validate([
			'reservation_id' => 'required|exists:reservations,id',
			'hotel_id' => 'required|exists:hotels,id',
			'city_id' => 'required|exists:cities,id',
			'meal_id' => 'required|exists:meals,id',
			'company_id' => 'required|exists:companies,id',
			'rate_id' => 'required|exists:rates,id',
			'check_in' => 'required|date',
			'check_out' => 'required|date|after:check_in',
			'rooms_count' => 'required|integer|min:1',
			'view' => 'required|string|max:255',
			'pax_count' => 'required|integer|min:1',
			'adults' => 'required|integer|min:1',
			'status' => 'required|string|in:new,in_revision,confirmed,refunded,cancelled,guaranteed',
			'children' => 'required|integer|min:0',
			'option_date' => 'required|date',
			'confirmation_number' => 'required|string|max:255',
			'price' => 'required|numeric|min:0',
		]);

		$hotelReservation = HotelReservation::create($request->only([
			'reservation_id',
			'hotel_id',
			'city_id',
			'meal_id',
			'company_id',
			'rate_id',
			'check_in',
			'check_out',
			'rooms_count',
			'view',
			'pax_count',
			'adults',
			'children',
			'option_date',
			'confirmation_number',
			'price',
		]));

		return send_response('Hotel reservation created successfully', 201, $hotelReservation);
	}

	public function show($id)
	{
		$hotelReservation = HotelReservation::with([
			'hotel',
			'reservation' => fn($query) => $query->with('client'),
			'meal',
			'company',
			'city',
			'rate'
		])->find($id);

		if (!$hotelReservation) {
			return send_response('Hotel reservation not found', 404);
		}

		return send_response('Hotel reservation retrieved successfully', 200, $hotelReservation);
	}

	public function update(Request $request, $id)
	{
		$hotelReservation = HotelReservation::find($id);

		if (!$hotelReservation) {
			return send_response('Hotel reservation not found', 404);
		}

		$request->validate([
			'reservation_id' => 'sometimes|exists:reservations,id',
			'hotel_id' => 'sometimes|exists:hotels,id',
			'city_id' => 'sometimes|exists:cities,id',
			'meal_id' => 'sometimes|exists:meals,id',
			'company_id' => 'sometimes|exists:companies,id',
			'rate_id' => 'sometimes|exists:rates,id',
			'check_in' => 'sometimes|date',
			'check_out' => 'sometimes|date|after:check_in',
			'rooms_count' => 'sometimes|integer|min:1',
			'view' => 'sometimes|string|max:255',
			'status' => 'sometimes|string|in:new,in_revision,confirmed,refunded,cancelled,guaranteed',
			'pax_count' => 'sometimes|integer|min:1',
			'adults' => 'sometimes|integer|min:1',
			'children' => 'sometimes|integer|min:0',
			'option_date' => 'sometimes|date',
			'confirmation_number' => 'sometimes|string|max:255',
			'price' => 'sometimes|numeric|min:0',
		]);

		$hotelReservation->update($request->only([
			'reservation_id',
			'hotel_id',
			'city_id',
			'meal_id',
			'company_id',
			'rate_id',
			'check_in',
			'check_out',
			'rooms_count',
			'view',
			'pax_count',
			'adults',
			'children',
			'option_date',
			'confirmation_number',
			'price',
		]));

		return send_response('Hotel reservation updated successfully', 200, $hotelReservation);
	}

	public function destroy($id)
	{
		$hotelReservation = HotelReservation::find($id);

		if (!$hotelReservation) {
			return send_response('Hotel reservation not found', 404);
		}

		$hotelReservation->delete();
		return send_response('Hotel reservation deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedHotelReservations = HotelReservation::with([
			'rate',
			'hotel',
			'meal',
			'city',
			'company',
			'room',
			'reservation' => fn($query) => $query->with('client')
		])->onlyTrashed()->paginate();
		return send_response('Deleted hotel reservations retrieved successfully', 200, $deletedHotelReservations);
	}

	public function restore($id)
	{
		$hotelReservation = HotelReservation::onlyTrashed()->find($id);

		if (!$hotelReservation) {
			return send_response('Deleted hotel reservation not found', 404);
		}

		$hotelReservation->restore();
		return send_response('Hotel reservation restored successfully', 200, $hotelReservation);
	}

	public function change_status($id, Request $request)
	{
		$request->validate([
			'status' => 'required|string|in:new,in_revision,confirmed,refunded,cancelled,guaranteed',
		]);

		$hotelReservation = HotelReservation::find($id);
		if (!$hotelReservation) {
			return send_response('Hotel reservation not found', 404);
		}

		HotelReservation::where('id', $id)->update(['status' => $request->status]);
		$hotelReservation = HotelReservation::find($id);
		return send_response('Hotel reservation status updated successfully', 200, $hotelReservation);
	}

	public function send_voucher($id)
	{
		$hotelReservation = HotelReservation::find($id);
		if (!$hotelReservation) {
			return send_response('Hotel reservation not found', 404);
		}
		HotelReservation::where('id', $id)->update(['status' => 'guaranteed']);
		$hotelReservation = HotelReservation::find($id);
		return send_response('Hotel reservation status updated successfully', 200, $hotelReservation);
	}
}
