<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelEmail;

class HotelEmailController extends Controller
{
	public function index(Request $request)
	{
		$search = $request->query('search');

		$orderBy = $request->query('orderBy', 'id');
		$orderType = $request->query('orderType', 'desc');

		$emails = HotelEmail::query();

		if ($search) {
			$emails->where('email', 'like', "%$search%");
		}

		$data = $emails->orderBy($orderBy, $orderType)->paginate();

		return send_response('categories retrieved successfully', 200, $data);
	}

	public function all(Request $request)
	{
		$search = $request->query('search');

		$emails = HotelEmail::query();

		if ($search) {
			$emails->where('email', 'like', "%$search%");
		}

		$emails = $emails->orderBy('id', 'desc')->take(20)->get();
		return send_response('categories retrieved successfully', 200, $emails);
	}

	public function trashed(Request $request)
	{
		$emails = HotelEmail::query()->onlyTrashed();
		$search = $request->query('search');


		if ($search) {
			$emails->where('email', 'like', "%$search%");
		}



		$data = $emails->paginate();
		return send_response('Trashed Citys retrieved successfully', 200, $data);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'email' => 'required|email|max:255|unique:hotel_emails',
			'hotel_id' => 'required|integer|exists:hotels,id',
		]);

		$email = HotelEmail::create($validated);
		return send_response('Email created successfully', 201, $email);
	}

	public function show($id)
	{
		$email = HotelEmail::find($id);
		if (!$email) {
			return send_response('Email not found', 404);
		}
		return send_response('Email retrieved successfully', 200, $email);
	}

	public function update(Request $request, $id)
	{
		$validated = $request->validate([
			'email' => 'sometimes|email|max:255|unique:hotel_emails,email,' . $id,
		]);

		$email = HotelEmail::find($id);

		if (!$email) {
			return send_response('Email not found', 404);
		}

		$email->update($validated);

		return send_response('Email updated successfully', 200, $email);
	}

	public function destroy($id)
	{
		$email = HotelEmail::find($id);
		if (!$email) {
			return send_response('Email not found', 404);
		}
		$email->delete();
		return send_response('Email deleted successfully', 200);
	}

	public function restore($id)
	{
		$email = HotelEmail::withTrashed()->find($id);

		if (!$email) {
			return send_response('Trashed Email not found', 404);
		}

		$email->restore();
		return send_response('Email restored successfully', 200, $email);
	}
}
