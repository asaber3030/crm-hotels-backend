<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required',
		]);

		if (!Auth::guard('agent')->attempt($request->only('email', 'password'))) {
			return send_response('Invalid Credentials', 401);
		}

		// $2y$12$hI6LRCJHQpk.50GGdLVRwexIof8giUxWUk9wdxw7p3SiUYa4SclwS

		/** @var User $user **/
		$user = Auth::guard('agent')->user();
		$token = $user->createToken('token')->plainTextToken;

		return response()->json([
			'message' => 'Login Successful',
			'status' => 200,
			'data' => [
				'token' => $token,
				'user' => $user,
			]
		]);
	}

	public function register(Request $request)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|confirmed',
		]);

		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
		]);

		$token = $user->createToken('token')->plainTextToken;

		return response()->json([
			'message' => 'Registration Successful',
			'status' => 201,
			'data' => [
				'token' => $token,
				'user' => $user,
			]
		]);
	}

	public function updateProfile(Request $request) {}

	public function me(Request $request)
	{
		$user = Auth::user('agent');
		return response()->json([
			'message' => 'Authorized',
			'data' => $user,
		]);
	}
}
