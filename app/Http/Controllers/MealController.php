<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;

class MealController extends Controller
{
	public function index()
	{
		$meals = Meal::orderBy('id', 'desc')->paginate();
		return send_response('Meals retrieved successfully', 200, $meals);
	}

	public function store(Request $request)
	{
		$request->validate([
			'meal_type' => 'required|string|max:255',
			'state' => 'required|string|in:pending,rejected,approved',
		]);

		$meal = Meal::create($request->only(['meal_type', 'state']));
		return send_response('Meal created successfully', 201, $meal);
	}

	public function show($id)
	{
		$meal = Meal::find($id);

		if (!$meal) {
			return send_response('Meal not found', 404);
		}

		return send_response('Meal retrieved successfully', 200, $meal);
	}

	public function update(Request $request, $id)
	{
		$meal = Meal::find($id);

		if (!$meal) {
			return send_response('Meal not found', 404);
		}

		$request->validate([
			'meal_type' => 'sometimes|string|max:255',
			'state' => 'sometimes|string|in:pending,rejected,approved',
		]);

		$meal->update($request->only(['meal_type', 'state']));
		return send_response('Meal updated successfully', 200, $meal);
	}

	public function destroy($id)
	{
		$meal = Meal::find($id);

		if (!$meal) {
			return send_response('Meal not found', 404);
		}

		$meal->delete();
		return send_response('Meal deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedMeals = Meal::onlyTrashed()->paginate();
		return send_response('Deleted meals retrieved successfully', 200, $deletedMeals);
	}

	public function restore($id)
	{
		$meal = Meal::onlyTrashed()->find($id);

		if (!$meal) {
			return send_response('Deleted meal not found', 404);
		}

		$meal->restore();
		return send_response('Meal restored successfully', 200, $meal);
	}
}
