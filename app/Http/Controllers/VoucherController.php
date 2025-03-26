<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;

class VoucherController extends Controller
{
	public function index(Request $request)
	{
		$vouchers = Voucher::query();

		if ($request->query('search')) {
			$search = $request->query('search');
			$vouchers->where('client_name', 'like', "%$search%");
		}

		$data = $vouchers
			->with(['hotel', 'meal', 'city', 'company', 'room'])
			->orderBy('id', 'desc')
			->paginate();
		return send_response('Vouchers retrieved successfully', 200, $data);
	}

	public function store(Request $request)
	{
		$request->validate([
			'room_id' => 'required|exists:rooms,id',
			'hotel_id' => 'required|exists:hotels,id',
			'city_id' => 'required|exists:cities,id',
			'meal_id' => 'required|exists:meals,id',
			'company_id' => 'required|exists:companies,id',
			'rate_id' => 'required|exists:rates,id',
			'payment_type_id' => 'required|exists:payments_types,id',
			'rooms_count' => 'required|integer|min:1',
			'view' => 'required|string|max:255',
			'pax' => 'required|integer|min:1',
			'adults' => 'required|integer|min:1',
			'children' => 'required|integer|min:0',
			'status' => 'required|string|in:pending,confirmed,cancelled',
			'internal_confirmation' => 'required|string|max:255',
			'hcn' => 'required|string|max:255',
			'nationality' => 'required|string|max:255',
			'client_name' => 'required|string|max:255',
			'notes' => 'nullable|string',
			'arrival_date' => 'required|date',
			'departure_date' => 'required|date',
			'nights' => 'required|integer|min:1',
			'holder_name' => 'required|string|max:255',
		]);

		$voucher = Voucher::create($request->only([
			'room_id',
			'hotel_id',
			'city_id',
			'meal_id',
			'company_id',
			'rate_id',
			'payment_type_id',
			'rooms_count',
			'view',
			'pax',
			'adults',
			'children',
			'status',
			'internal_confirmation',
			'hcn',
			'nationality',
			'client_name',
			'notes',
			'arrival_date',
			'departure_date',
			'nights',
			'holder_name',
		]));

		return send_response('Voucher created successfully', 201, $voucher);
	}

	public function show($id)
	{
		$voucher = Voucher::with([
			'hotel',
			'meal',
			'company',
			'city',
			'room',
			'rate',
			'payment_type'
		])->find($id);

		if (!$voucher) {
			return send_response('Voucher not found', 404);
		}

		return send_response('Voucher retrieved successfully', 200, $voucher);
	}

	public function update(Request $request, $id)
	{
		$voucher = Voucher::find($id);

		if (!$voucher) {
			return send_response('Voucher not found', 404);
		}

		$request->validate([
			'room_id' => 'sometimes|exists:rooms,id',
			'hotel_id' => 'sometimes|exists:hotels,id',
			'city_id' => 'sometimes|exists:cities,id',
			'meal_id' => 'sometimes|exists:meals,id',
			'company_id' => 'sometimes|exists:companies,id',
			'payment_type_id' => 'sometimes|exists:payments_types,id',
			'rate_id' => 'sometimes|exists:rates,id',
			'rooms_count' => 'sometimes|integer|min:1',
			'view' => 'sometimes|string|max:255',
			'pax' => 'sometimes|integer|min:1',
			'adults' => 'sometimes|integer|min:1',
			'children' => 'sometimes|integer|min:0',
			'status' => 'sometimes|string|in:pending,confirmed,cancelled',
			'internal_confirmation' => 'sometimes|string|max:255',
			'hcn' => 'sometimes|string|max:255',
			'nationality' => 'sometimes|string|max:255',
			'client_name' => 'sometimes|string|max:255',
			'notes' => 'sometimes|string',
			'arrival_date' => 'sometimes|date',
			'departure_date' => 'sometimes|date|after:arrival_date',
			'nights' => 'sometimes|integer|min:1',
		]);

		$voucher->update($request->only([
			'room_id',
			'hotel_id',
			'city_id',
			'meal_id',
			'company_id',
			'payment_type_id',
			'rate_id',
			'rooms_count',
			'view',
			'pax',
			'adults',
			'children',
			'status',
			'internal_confirmation',
			'hcn',
			'nationality',
			'client_name',
			'notes',
			'arrival_date',
			'departure_date',
			'nights',
		]));

		return send_response('Voucher updated successfully', 200, $voucher);
	}

	public function destroy($id)
	{
		$voucher = Voucher::find($id);

		if (!$voucher) {
			return send_response('Voucher not found', 404);
		}

		$voucher->delete();
		return send_response('Voucher deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedHotelReservations = Voucher::onlyTrashed()->paginate();
		return send_response('Deleted Vouchers retrieved successfully', 200, $deletedHotelReservations);
	}

	public function restore($id)
	{
		$voucher = Voucher::onlyTrashed()->find($id);

		if (!$voucher) {
			return send_response('Deleted Voucher not found', 404);
		}

		$voucher->restore();
		return send_response('Voucher restored successfully', 200, $voucher);
	}

	public function show_pdf($id)
	{
		$voucher = Voucher::with([
			'hotel',
			'meal',
			'company',
			'city',
			'room'
		])->find($id);

		if (!$voucher) {
			return send_response('Voucher not found', 404);
		}

		$pdf = Pdf::loadView('vouchers.pdf', compact('voucher'));
		return $pdf->stream("voucher_{$voucher->id}.pdf");
	}
}
