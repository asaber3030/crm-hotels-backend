<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{

	public function index(Request $request)
	{
		$search = $request->query('search');
		$cities = City::query();

		if ($search) {
			$cities->where('name', 'like', "%$search%")
				->orWhere('email', 'like', "%$search%")
				->orWhere('phone', 'like', "%$search%");
		}

		$data = $cities->orderBy('id', 'desc')->paginate();

		return send_response('Cities retrieved successfully', 200, $data);
	}

	public function all(Request $request)
	{
		$search = $request->query('search');
		$cities = City::query();

		if ($search) {
			$cities->where('name', 'like', "%$search%");
		}

		$cities = $cities->orderBy('id', 'desc')->take(20)->get();
		return send_response('Cities retrieved successfully', 200, $cities);
	}

	public function trashed(Request $request)
	{
		$cities = City::query()->onlyTrashed();
		$search = $request->query('search');

		if ($search) {
			$cities->where('name', 'like', "%$search%");
		}

		$data = $cities->paginate();
		return send_response('Trashed Citys retrieved successfully', 200, $data);
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'state' => 'required|string|max:255',
		]);

		$city = City::create($request->all());
		return send_response('City created successfully', 201, $city);
	}

	public function show($id)
	{
		$city = City::find($id);
		if (!$city) {
			return send_response('City not found', 404);
		}
		return send_response('City retrieved successfully', 200, $city);
	}

	public function update(Request $request, $id)
	{
		$request->validate([
			'name' => 'sometimes|required|string|max:255',
			'state' => 'sometimes|required|string|max:255',
		]);

		$city = City::find($id);
		if (!$city) {
			return send_response('City not found', 404);
		}
		$city->update($request->all());
		return send_response('City updated successfully', 200, $city);
	}

	public function destroy($id)
	{
		$city = City::find($id);
		if (!$city) {
			return send_response('City not found', 404);
		}
		$city->delete();
		return send_response('City deleted successfully', 204);
	}

	public function restore($id)
	{
		$city = City::withTrashed()->find($id);

		if (!$city) {
			return send_response('Trashed City not found', 404);
		}

		$city->restore();
		return send_response('City restored successfully', 200, $city);
	}
}
