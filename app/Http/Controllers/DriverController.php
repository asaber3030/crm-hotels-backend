<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{
	public function index()
	{
		$drivers = Driver::orderBy('id', 'desc')->simplePaginate();
		return send_response('Drivers retrieved successfully', 200, $drivers);
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'phone' => 'required|string|max:15|unique:drivers,phone',
		]);

		$driver = Driver::create($request->only(['name', 'phone']));
		return send_response('Driver created successfully', 201, $driver);
	}

	public function show($id)
	{
		$driver = Driver::find($id);
		if (!$driver) {
			return send_response('Driver not found', 404);
		}
		return send_response('Driver retrieved successfully', 200, $driver);
	}

	public function update(Request $request, $id)
	{
		$driver = Driver::find($id);
		if (!$driver) {
			return send_response('Driver not found', 404);
		}
		$request->validate([
			'name' => 'sometimes|string|max:255',
			'phone' => 'sometimes|string|max:15|unique:drivers,phone,' . $id . ',id',
		]);
		$driver->update($request->only(['name', 'phone']));
		return send_response('Driver updated successfully', 200, $driver);
	}

	public function destroy($id)
	{
		$driver = Driver::find($id);
		if (!$driver) {
			return send_response('Driver not found', 404);
		}
		$driver->delete();
		return send_response('Driver deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedDrivers = Driver::onlyTrashed()->simplePaginate();
		return send_response('Deleted drivers retrieved successfully', 200, $deletedDrivers);
	}

	public function restore($id)
	{
		$driver = Driver::onlyTrashed()->find($id);
		if (!$driver) {
			return send_response('Deleted driver not found', 404);
		}

		$driver->restore();
		return send_response('Driver restored successfully', 200, $driver);
	}
}
