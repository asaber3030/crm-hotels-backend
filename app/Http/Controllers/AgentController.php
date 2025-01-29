<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
	public function index()
	{
		$agents = Agent::orderBy('id', 'desc')->simplePaginate();
		return send_response('Agents retrieved successfully', 200, $agents);
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:agents,email',
			'password' => 'required|string|min:8',
			'contact_number' => 'required|string|max:15',
			'address' => 'required|string|max:255',
			'role' => 'required|string|in:admin,super_admin,agent',
			'state' => 'required|string|in:pending,approved',
		]);
		$agent = Agent::create([
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'contact_number' => $request->input('contact_number'),
			'address' => $request->input('address'),
			'role' => $request->input('role'),
			'state' => $request->input('state'),
			'password' => Hash::make($request->input('password')),
		]);
		return send_response('Agent created successfully', 201, $agent);
	}

	public function show($id)
	{
		$agent = Agent::find($id);

		if (!$agent) {
			return send_response('Agent not found', 404);
		}

		return send_response('Agent retrieved successfully', 200, $agent);
	}

	public function update(Request $request, $id)
	{
		$agent = Agent::find($id);
		$request->validate([
			'name' => 'sometimes|string|max:255',
			'email' => 'sometimes|email|unique:agents,email,' . $id . ',id',
			'contact_number' => 'sometimes|string|max:15',
			'address' => 'sometimes|string|max:255',
			'role' => 'sometimes|string|in:admin,super_admin,agent',
			'state' => 'sometimes|string|in:pending,approved',
		]);
		if (!$agent) {
			return send_response('Agent not found', 404);
		}
		$agent->update($request->only(['name', 'email', 'contact_number', 'address', 'role', 'state']));
		return send_response('Agent updated successfully', 200, $agent);
	}

	public function destroy($id)
	{
		$agent = Agent::find($id);

		if (!$agent) {
			return send_response('Agent not found', 404);
		}
		$agent->delete();
		return send_response('Agent deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedAgents = Agent::onlyTrashed()->simplePaginate();
		return send_response('Deleted agents retrieved successfully', 200, $deletedAgents);
	}

	public function restore($id)
	{
		$agent = Agent::onlyTrashed()->find($id);
		if (!$agent) {
			return send_response('Deleted agent not found', 404);
		}
		$agent->restore();
		return send_response('Agent restored successfully', 200, $agent);
	}
}
