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
			'client_name' => 'required|string|max:255',
			'nationality' => 'required|string|max:255',
			'check_in' => 'required|date',
			'check_out' => 'required|date|after:check_in',
			'rooms_count' => 'required|integer|min:1',
			'pax' => 'required|integer|min:1',
			'adults' => 'required|integer|min:1',
			'children' => 'required|integer|min:0',
			'internal_confirmation' => 'required|string|max:255',
			'view' => 'required|string|max:255',
			'hcn' => 'required|string|max:255',
			'notes' => 'required|string',
		]);

		$voucher = Voucher::create($request->only([
			'room_id',
			'hotel_id',
			'city_id',
			'meal_id',
			'company_id',
			'client_name',
			'nationality',
			'check_in',
			'check_out',
			'rooms_count',
			'pax',
			'adults',
			'children',
			'view',
			'internal_confirmation',
			'hcn',
			'notes',
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
			'client_name' => 'sometimes|string|max:255',
			'nationality' => 'sometimes|string|max:255',
			'check_in' => 'sometimes|date',
			'check_out' => 'sometimes|date|after:check_in',
			'rooms_count' => 'sometimes|integer|min:1',
			'pax' => 'sometimes|integer|min:1',
			'adults' => 'sometimes|integer|min:1',
			'children' => 'sometimes|integer|min:0',
			'internal_confirmation' => 'sometimes|string|max:255',
			'view' => 'sometimes|string|max:255',
			'hcn' => 'sometimes|string|max:255',
			'notes' => 'sometimes|string',
		]);

		$voucher->update($request->only([
			'room_id',
			'hotel_id',
			'city_id',
			'meal_id',
			'company_id',
			'client_name',
			'nationality',
			'check_in',
			'check_out',
			'rooms_count',
			'pax',
			'adults',
			'children',
			'view',
			'internal_confirmation',
			'hcn',
			'notes',
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
