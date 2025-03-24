<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->query('search');
    $payments = PaymentType::query();

    if ($search) {
      $payments->where('name', 'like', '%' . $search . '%');
    }

    $data = $payments->orderBy('id', 'desc')->paginate();
    return send_response('Payments Types retrieved successfully', 200, $data);
  }

  public function all(Request $request)
  {
    $search = $request->query('search');
    $payments = PaymentType::query();

    if ($search) {
      $payments->where('name', 'like', '%' . $search . '%');
    }

    $data = $payments->orderBy('id', 'desc')->take(20)->get();
    return send_response('Payments Types retrieved successfully', 200, $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:payments_types,name',
      'state' => 'required|string|in:active,inactive',
    ]);

    $payment_type = PaymentType::create($request->only(['name', 'state']));
    return send_response('Payment Type created successfully', 201, $payment_type);
  }

  public function show($id)
  {
    $payment_type = PaymentType::find($id);

    if (!$payment_type) {
      return send_response('PaymentType not found', 404);
    }

    return send_response('PaymentType retrieved successfully', 200, $payment_type);
  }

  public function update(Request $request, $id)
  {
    $payment_type = PaymentType::find($id);

    if (!$payment_type) {
      return send_response('Payment Type not found', 404);
    }

    $request->validate([
      'name' => 'sometimes|string|max:255|unique:payments_types,name,' . $id . ',id',
      'state' => 'sometimes|string|in:active,inactive',
    ]);

    $payment_type->update($request->only(['name', 'state']));
    return send_response('Payment Type updated successfully', 200, $payment_type);
  }

  public function destroy($id)
  {
    $payment_type = PaymentType::find($id);

    if (!$payment_type) {
      return send_response('Payment Type not found', 404);
    }

    $payment_type->delete();
    return send_response('Payment Type deleted successfully', 200);
  }

  public function trashed()
  {
    $deletedtypes = PaymentType::onlyTrashed()->paginate();
    return send_response('Deleted types retrieved successfully', 200, $deletedtypes);
  }

  public function restore($id)
  {
    $type = PaymentType::onlyTrashed()->find($id);
    $type->restore();
    return send_response('Payment Type restored successfully', 200, $type);
  }
}
