<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
	public function index()
	{
		$search = request()->query('search');
		$orderBy = request()->query('orderBy', 'id');
		$orderDirection = request()->query('orderType', 'desc');
		$invoices = Invoice::query();

		if ($search) {
			$invoices->where(function ($query) use ($search) {
				$query->where('customer_name', 'like', "%{$search}%")
					->orWhere('proxy_name', 'like', "%{$search}%")
					->orWhere('reservation_number', 'like', "%{$search}%");
			});
		}

		$invoices->with('agent', 'hotel', 'category', 'payment', 'collection');
		$data = $invoices->orderBy($orderBy, $orderDirection)->paginate(15);
		return send_response('Invoices', 200, $data);
	}

	public function trashed()
	{
		$search = request()->query('search');
		$orderBy = request()->query('orderBy', 'id');
		$orderDirection = request()->query('orderType', 'desc');
		$invoices = Invoice::query();

		if ($search) {
			$invoices->where(function ($query) use ($search) {
				$query->where('customer_name', 'like', "%{$search}%")
					->orWhere('proxy_name', 'like', "%{$search}%")
					->orWhere('reservation_number', 'like', "%{$search}%");
			});
		}

		$invoices->with('agent', 'hotel', 'category', 'payment', 'collection');
		$invoices->onlyTrashed();
		$data = $invoices->orderBy($orderBy, $orderDirection)->paginate(15);
		return send_response('Trashed Invoices', 200, $data);
	}

	public function agent_invoices($id)
	{
		$search = request()->query('search');
		$orderBy = request()->query('orderBy', 'id');
		$orderDirection = request()->query('orderType', 'desc');
		$invoices = Invoice::query();

		if ($search) {
			$invoices->where(function ($query) use ($search) {
				$query->where('customer_name', 'like', "%{$search}%")
					->orWhere('proxy_name', 'like', "%{$search}%")
					->orWhere('reservation_number', 'like', "%{$search}%");
			});
		}

		$invoices->where('agent_id', $id);
		$data = $invoices->orderBy($orderBy, $orderDirection)->paginate(15);

		return send_response('Invoices', 200, $data);
	}

	public function show($id)
	{
		try {
			$invoice = Invoice::with(['agent', 'hotel', 'category', 'payment', 'collection'])->findOrFail($id);
			return send_response('Invoice details', 200, $invoice);
		} catch (\Exception $e) {
			return send_response($e->getMessage(), 500);
		}
	}

	public function store(Request $request)
	{
		try {
			$request->validate([
				'hotel_id' => 'required|exists:hotels,id',
				'category_id' => 'required|exists:categories,id',
				'amount' => 'required|numeric',
				'creation_date' => 'required|date',
				'from1' => 'required|date',
				'to1' => 'required|date',
				'from2' => 'nullable|string',
				'to2' => 'nullable|string',
				'customer_name' => 'required|string|max:255',
				'proxy_name' => 'required|string|max:255',
				'reservation_number' => 'required|string|max:255',
				'nights_count' => 'required|numeric',

				'collection.amount_egp' => 'required|numeric',
				'collection.amount_sar' => 'required|numeric',
				'collection.amount_usd' => 'required|numeric',
				'collection.link' => 'required|string|max:255',

				'payment.amount_egp' => 'required|numeric',
				'payment.amount_sar' => 'required|numeric',
				'payment.amount_usd' => 'required|numeric',
			]);

			$invoice = Invoice::create([
				'agent_id' => Auth::id(),
				'hotel_id' => $request->input('hotel_id'),
				'category_id' => $request->input('category_id'),
				'amount' => $request->input('amount'),
				'creation_date' => \Carbon\Carbon::parse($request->input('creation_date'))->format('Y-m-d'),
				'from1' => \Carbon\Carbon::parse($request->input('from1'))->format('Y-m-d'),
				'to1' => \Carbon\Carbon::parse($request->input('to1'))->format('Y-m-d'),
				'from2' => $request->input('from2'),
				'to2' => $request->input('to2'),
				'customer_name' => $request->input('customer_name'),
				'proxy_name' => $request->input('proxy_name'),
				'reservation_number' => $request->input('reservation_number'),
				'nights_count' => $request->input('nights_count')
			]);

			if ($request->has('collection')) {
				$invoice->collection()->create([
					'invoice_id' => $invoice->id,
					'amount_egp' => $request->collection['amount_egp'],
					'amount_sar' => $request->collection['amount_sar'],
					'amount_usd' => $request->collection['amount_usd'],
					'link' => $request->collection['link'],
				]);
			}

			if ($request->has('payment')) {
				$invoice->payment()->create([
					'invoice_id' => $invoice->id,
					'amount_egp' => $request->payment['amount_egp'],
					'amount_sar' => $request->payment['amount_sar'],
					'amount_usd' => $request->payment['amount_usd'],
				]);
			}

			return send_response('Invoice created successfully', 201, $invoice);
		} catch (\Exception $e) {
			return send_response($e->getMessage(), 500);
		}
	}

	public function update(Request $request, $id)
	{
		try {
			$request->validate([
				'hotel_id' => 'required|exists:hotels,id',
				'category_id' => 'required|exists:categories,id',
				'amount' => 'required|string',
				'creation_date' => 'required|date',
				'from1' => 'required|date',
				'to1' => 'required|date',
				'from2' => 'nullable|string',
				'to2' => 'nullable|string',
				'customer_name' => 'required|string|max:255',
				'proxy_name' => 'required|string|max:255',
				'reservation_number' => 'required|string|max:255',
				'nights_count' => 'required|numeric',

				'collection.amount_egp' => 'required|string',
				'collection.amount_sar' => 'required|string',
				'collection.amount_usd' => 'required|string',
				'collection.link' => 'required|string|max:255',

				'payment.amount_egp' => 'required|string',
				'payment.amount_sar' => 'required|string',
				'payment.amount_usd' => 'required|string',
			]);

			$invoice = Invoice::findOrFail($id);

			$invoice->update([

				'hotel_id' => $request->input('hotel_id'),
				'category_id' => $request->input('category_id'),
				'amount' => $request->input('amount'),
				'creation_date' => \Carbon\Carbon::parse($request->input('creation_date'))->format('Y-m-d'),
				'from1' => \Carbon\Carbon::parse($request->input('from1'))->format('Y-m-d'),
				'to1' => \Carbon\Carbon::parse($request->input('to1'))->format('Y-m-d'),
				'from2' => $request->input('from2'),
				'to2' => $request->input('to2'),
				'customer_name' => $request->input('customer_name'),
				'proxy_name' => $request->input('proxy_name'),
				'reservation_number' => $request->input('reservation_number'),
				'nights_count' => $request->input('nights_count')
			]);

			$invoice->collection()->delete();
			$invoice->payment()->delete();

			$invoice->collection()->create([
				'invoice_id' => $invoice->id,
				'amount_egp' => $request->collection['amount_egp'],
				'amount_sar' => $request->collection['amount_sar'],
				'amount_usd' => $request->collection['amount_usd'],
				'link' => $request->collection['link']
			]);

			$invoice->payment()->create([
				'invoice_id' => $invoice->id,
				'amount_egp' => $request->payment['amount_egp'],
				'amount_sar' => $request->payment['amount_sar'],
				'amount_usd' => $request->payment['amount_usd']
			]);

			return send_response('Invoice updated successfully', 200, $invoice);
		} catch (\Exception $e) {
			return send_response($e->getMessage(), 500);
		}
	}

	public function destroy($id)
	{
		try {
			$invoice = Invoice::findOrFail($id);
			$invoice->delete();
			return send_response('Invoice deleted successfully', 200);
		} catch (\Exception $e) {
			return send_response($e->getMessage(), 500);
		}
	}

	public function restore($id)
	{
		try {
			$invoice = Invoice::withTrashed()->findOrFail($id);
			$invoice->restore();
			return send_response('Invoice restored successfully', 200, $invoice);
		} catch (\Exception $e) {
			return send_response($e->getMessage(), 500);
		}
	}
}
