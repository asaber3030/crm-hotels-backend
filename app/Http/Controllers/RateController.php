<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
  public function index()
  {
    $rates = Rate::paginate();
    return send_response('Rates retrieved successfully', 200, $rates);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:rates,name',
      'state' => 'required|string|in:pending,approved,rejected',
    ]);

    $rate = Rate::create($request->only(['name', 'state']));
    return send_response('Rate created successfully', 201, $rate);
  }

  public function show($id)
  {
    $rate = Rate::find($id);

    if (!$rate) {
      return send_response('Rate not found', 404);
    }

    return send_response('Rate retrieved successfully', 200, $rate);
  }

  public function update(Request $request, $id)
  {
    $rate = Rate::find($id);

    if (!$rate) {
      return send_response('Rate not found', 404);
    }

    $request->validate([
      'name' => 'sometimes|string|max:255|unique:rates,name,' . $id . ',id',
      'state' => 'sometimes|string|in:pending,approved,rejected',
    ]);

    $rate->update($request->only(['name', 'state']));
    return send_response('Rate updated successfully', 200, $rate);
  }

  public function destroy($id)
  {
    $rate = Rate::find($id);

    if (!$rate) {
      return send_response('Rate not found', 404);
    }

    $rate->delete();
    return send_response('Rate deleted successfully', 200);
  }

  public function trashed()
  {
    $deletedRates = Rate::onlyTrashed()->simplePaginate();
    return send_response('Deleted rates retrieved successfully', 200, $deletedRates);
  }

  public function restore($id)
  {
    $rate = Rate::onlyTrashed()->find($id);

    if (!$rate) {
      return send_response('Deleted rate not found', 404);
    }

    $rate->restore();
    return send_response('Rate restored successfully', 200, $rate);
  }
}
