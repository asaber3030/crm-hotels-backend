<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{



	public function index()
	{
		$users = User::orderBy('id', 'desc')->simplePaginate();
		return send_response('Users retrieved successfully', 200, $users);
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|string|min:8',
			'contact_number' => 'required|string|unique:users|max:11|regex:/^01[0125][0-9]{8}$/',
			'address' => 'required|string|max:255',
			'role' => 'required|string|in:admin,user,super_admin',
		]);

		$user = User::create([
			...$request->only(['name', 'email', 'contact_number', 'address', 'role']),
			'password' => Hash::make($request->input('password')),
		]);
		return send_response('User created successfully', 201, $user);
	}

	public function show($id)
	{
		$user = User::find($id);

		if (!$user) {
			return send_response('User not found', 404);
		}

		return send_response('User retrieved successfully', 200, $user);
	}

	public function update(Request $request, $id)
	{
		$user = User::find($id);

		if (!$user) {
			return send_response('User not found', 404);
		}

		$request->validate([
			'name' => 'sometimes|string|max:255',
			'email' => 'sometimes|email|unique:users,email,' . $id,
			'password' => 'sometimes|string|min:8',
			'contact_number' => 'sometimes|string|max:11|regex:/^01[0125][0-9]{8}$/|unique:users,contact_number,' . $id,
			'address' => 'sometimes|string|max:255',
			'role' => 'sometimes|string|in:admin,user,super_admin',
		]);

		$user->update([
			...$request->only(['name', 'email', 'contact_number', 'address', 'role']),
			'password' => Hash::make($request->input('password')),
		]);
		return send_response('User updated successfully', 200, $user);
	}

	public function destroy($id)
	{
		$user = User::find($id);

		if (!$user) {
			return send_response('User not found', 404);
		}

		$user->delete();
		return send_response('User deleted successfully', 200);
	}

	public function trashed()
	{
		$deletedUsers = User::onlyTrashed()->simplePaginate();
		return send_response('Deleted Users retrieved successfully', 200, $deletedUsers);
	}

	public function restore($id)
	{
		$user = User::onlyTrashed()->find($id);
		if (!$user) {
			return send_response('Deleted User not found', 404);
		}
		$user->restore();
		return send_response('User restored successfully', 200, $user);
	}
}
