<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->query('search');
    $orderBy = $request->query('orderBy', 'id');
    $orderType = $request->query('orderType', 'desc');

    $currencies = Currency::query();

    if ($search) {
      $currencies
        ->where('name', 'like', "%$search%")
        ->orWhere('code', 'like', "%$search%");
    }

    $data = $currencies->orderBy($orderBy, $orderType)->paginate();

    return send_response('Currencies retrieved successfully', 200, $data);
  }

  public function all(Request $request)
  {
    $search = $request->query('search');
    $orderBy = $request->query('orderBy', 'id');
    $orderType = $request->query('orderType', 'desc');
    $currencies = Currency::query();


    if ($search) {
      $currencies->where('name', 'like', "%$search%");
    }

    $currencies->where('is_active', 1);
    $currencies = $currencies->orderBy($orderBy, $orderType)->take(20)->get();
    return send_response('Currencies retrieved successfully', 200, $currencies);
  }

  public function trashed(Request $request)
  {
    $currencies = Currency::query()->onlyTrashed();
    $orderBy = $request->query('orderBy', 'id');
    $orderType = $request->query('orderType', 'desc');
    $search = $request->query('search');

    if ($search) {
      $currencies
        ->where('name', 'like', "%$search%")
        ->orWhere('code', 'like', "%$search%");
    }

    $data = $currencies->orderBy($orderBy, $orderType)->paginate();
    return send_response('Trashed Currencies retrieved successfully', 200, $data);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'code' => 'required|string|max:10',
      'value' => 'required|numeric',
      'is_active' => 'nullable|boolean',
    ]);

    $currency = Currency::create([
      'name' => $validated['name'],
      'code' => $validated['code'],
      'value' => $validated['value'],
      'is_active' => $validated['is_active'],
    ]);
    return send_response('Currency created successfully', 201, $currency);
  }

  public function show($id)
  {
    $currency = Currency::find($id);
    if (!$currency) {
      return send_response('Currency not found', 404);
    }
    return send_response('Currency retrieved successfully', 200, $currency);
  }

  public function update(Request $request, $id)
  {
    $validated = $request->validate([
      'name' => 'sometimes|string|max:255',
      'code' => 'sometimes|string|max:10',
      'value' => 'sometimes|numeric',
      'is_active' => 'sometimes|boolean',
    ]);

    $currency = Currency::find($id);

    if (!$currency) {
      return send_response('Currency not found', 404);
    }

    $currency->update([
      'name' => $validated['name'] ?? $currency->name,
      'code' => $validated['code'] ?? $currency->code,
      'value' => $validated['value'] ?? $currency->value,
      'is_active' => $validated['is_active'] ?? $currency->is_active,
    ]);

    return send_response('Currency updated successfully', 200, $currency);
  }

  public function destroy($id)
  {
    $currency = Currency::find($id);
    if (!$currency) {
      return send_response('Currency not found', 404);
    }
    $currency->delete();
    return send_response('Currency deleted successfully', 200);
  }

  public function restore($id)
  {
    $currency = Currency::withTrashed()->find($id);

    if (!$currency) {
      return send_response('Trashed Currency not found', 404);
    }

    $currency->restore();
    return send_response('Currency restored successfully', 200, $currency);
  }
}
