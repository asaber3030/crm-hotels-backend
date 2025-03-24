<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
	public function index(Request $request)
	{
		$rooms = Room::query();
		if ($request->query('search')) {
			$rooms->where('room_type', 'like', "%{$request->query('search')}%")
				->whereHas('hotel', function ($query) use ($request) {
					$query->where('name', 'like', "%{$request->query('search')}%");
				});
		}
		$data = $rooms->orderBy('id', 'desc')->with('hotel')->paginate();
		return send_response('Rooms retrieved successfully', 200, $data);
	}

	public function all(Request $request)
	{
		$rooms = Room::query();
		if ($request->query('search')) {
			$rooms->where('room_type', 'like', "%{$request->query('search')}%");
		}
		$data = $rooms->orderBy('id', 'desc')->take(20)->get();
		return send_response('Rooms retrieved successfully', 200, $data);
	}

	public function store(Request $request)
	{
		$request->validate([
			'hotel_id' => 'required|exists:hotels,id',
			'room_type' => 'required',
		]);

		$room = Room::create($request->only([
			'hotel_id',
			'room_type',
		]));

		return send_response('Room created successfully', 201, $room);
	}

	public function show($id)
	{
		$room = Room::with('hotel')->find($id);
		if (!$room) {
			return send_response('Room not found', 404);
		}
		return send_response('Room retrieved successfully', 200, $room);
	}

	public function update(Request $request, $id)
	{
		$room = Room::find($id);

		if (!$room) {
			return send_response('Room not found', 404);
		}

		$request->validate([
			'hotel_id' => 'required|exists:hotels,id',
			'room_type' => 'required',
		]);

		$room->update($request->only([
			'hotel_id',
			'room_type',
		]));

		return send_response('Room updated successfully', 200, $room);
	}

	public function destroy($id)
	{
		$room = Room::find($id);
		if (!$room) {
			return send_response('Room not found', 404);
		}
		$room->delete();
		return send_response('Room deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedRooms = Room::with('hotel')->onlyTrashed()->paginate();
		return send_response('Deleted rooms retrieved successfully', 200, $deletedRooms);
	}

	public function restore($id)
	{
		$room = Room::onlyTrashed()->find($id);
		if (!$room) {
			return send_response('Deleted room not found', 404);
		}
		$room->restore();
		return send_response('Room restored successfully', 200, $room);
	}
}
