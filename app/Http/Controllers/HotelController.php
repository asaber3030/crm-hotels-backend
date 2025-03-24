<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Room;

class HotelController extends Controller
{

	public function index(Request $request)
	{
		$search = $request->query('search');
		$hotels = Hotel::query();
		if ($search) {
			$hotels->where('name', 'like', "%$search%")
				->orWhere('email', 'like', "%$search%")
				->orWhere('phone_number', 'like', "%$search%");
		}
		$data = $hotels->orderBy('id', 'desc')->with('city')->paginate();
		return send_response('Hotels retrieved successfully', 200, $data);
	}

	public function filter_by_city(Request $request, string $city)
	{
		$search = $request->query('search');
		$take = $request->query('take');

		$hotels = Hotel::query();
		$hotels->where('city_id', $city);

		if ($search) {
			$hotels->where('name', 'like', "%$search%");
		}

		$hotels = $hotels->orderBy('id', 'desc')->take($take ?? 20)->get();
		return send_response('Hotels retrieved successfully', 200, $hotels);
	}

	public function all(Request $request)
	{
		$search = $request->query('search');
		$take = $request->query('take');

		$hotels = Hotel::query();

		if ($search) {
			$hotels->where('name', 'like', "%$search%");
		}

		$hotels = $hotels->orderBy('id', 'desc')->take($take ?? 20)->get();
		return send_response('Hotels retrieved successfully', 200, $hotels);
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'city_id' => 'required|exists:cities,id',
			'phone_number' => 'nullable|string|max:20',
			'email' => 'nullable|email|max:255',
		]);

		$hotel = Hotel::create($request->only(['name', 'city_id', 'phone_number', 'email']));
		return send_response('Hotel created successfully', 201, $hotel);
	}

	public function show($id)
	{
		$hotel = Hotel::with('city')->find($id);
		if (!$hotel) {
			return send_response('Hotel not found', 404);
		}
		return send_response('Hotel retrieved successfully', 200, $hotel);
	}


	public function update(Request $request, $id)
	{
		$hotel = Hotel::find($id);
		if (!$hotel) {
			return send_response('Hotel not found', 404);
		}
		$request->validate([
			'name' => 'sometimes|required|string|max:255',
			'city_id' => 'sometimes|required|exists:cities,id',
			'phone_number' => 'nullable|string|max:20',
			'email' => 'nullable|email|max:255',
		]);
		$hotel->update($request->only(['name', 'city_id', 'phone_number', 'email']));
		return send_response('Hotel updated successfully', 200, $hotel);
	}

	public function destroy($id)
	{
		$hotel = Hotel::find($id);
		if (!$hotel) {
			return send_response('Hotel not found', 404);
		}
		$hotel->delete();
		return send_response('Hotel deleted successfully', 204);
	}

	public function restore($id)
	{
		$hotel = Hotel::onlyTrashed()->find($id);

		if (!$hotel) {
			return send_response('Trashed hotel not found', 404);
		}

		$hotel->restore();
		return send_response('Hotel restored successfully', 200, $hotel);
	}

	public function trashed()
	{
		$hotels = Hotel::onlyTrashed()->with('city')->get();
		return send_response('Trashed hotels retrieved successfully', 200, $hotels);
	}

	public function hotelRooms($id)
	{
		$rooms = Room::where('hotel_id', $id)->get();
		return send_response('Hotel rooms retrieved successfully', 200, $rooms);
	}
}
