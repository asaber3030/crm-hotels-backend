<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
	public function index(Request $request)
	{
		$search = $request->query('search');
		$clients = Client::query();
		if ($search) {
			$clients->where('name', 'like', "%$search%")
				->orWhere('email', 'like', "%$search%")
				->orWhere('phone', 'like', "%$search%");
		}
		$data = $clients->orderBy('id', 'desc')->paginate();
		return send_response('Clients retrieved successfully', 200, $data);
	}

	public function all(Request $request)
	{
		$search = $request->query('search');
		$clients = Client::query();
		if ($search) {
			$clients->where('name', 'like', "%$search%")
				->orWhere('email', 'like', "%$search%")
				->orWhere('phone', 'like', "%$search%");
		}
		$data = $clients->orderBy('id', 'desc')->take(20)->get();
		return send_response('Clients retrieved successfully', 200, $data);
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:clients,email',
			'phone' => 'required|string|unique:clients,phone',
			'nationality' => 'required|string|max:255',
		]);

		$client = Client::create($request->only(['name', 'email', 'phone', 'nationality']));
		return send_response('Client created successfully', 201, $client);
	}

	public function show($id)
	{
		$client = Client::find($id);

		if (!$client) {
			return send_response('Client not found', 404);
		}

		return send_response('Client retrieved successfully', 200, $client);
	}

	public function update(Request $request, $id)
	{
		$client = Client::find($id);

		if (!$client) {
			return send_response('Client not found', 404);
		}

		$request->validate([
			'name' => 'sometimes|string|max:255',
			'email' => 'sometimes|email|unique:clients,email,' . $id,
			'phone' => 'sometimes|string|unique:clients,phone,' . $id,
			'nationality' => 'sometimes|string|max:255',
		]);

		$client->update($request->only(['name', 'email', 'phone', 'nationality']));
		return send_response('Client updated successfully', 200, $client);
	}

	public function destroy($id)
	{
		$client = Client::find($id);

		if (!$client) {
			return send_response('Client not found', 404);
		}

		$client->delete();
		return send_response('Client deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedClients = Client::onlyTrashed()->paginate();
		return send_response('Deleted clients retrieved successfully', 200, $deletedClients);
	}

	public function restore($id)
	{
		$client = Client::withTrashed()->find($id);
		if (!$client) {
			return send_response('Deleted client not found', 404);
		}
		$client->restore();
		return send_response('Client restored successfully', 200, $client);
	}
}
