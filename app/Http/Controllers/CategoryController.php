<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
	public function index(Request $request)
	{
		$search = $request->query('search');
		$type = $request->query('type');
		$orderBy = $request->query('orderBy', 'id');
		$orderType = $request->query('orderType', 'desc');

		$categories = Category::query();

		if ($search) {
			$categories->where('name', 'like', "%$search%");
		}

		if ($type) {
			$categories->where('type', $type);
		}


		$data = $categories->orderBy($orderBy, $orderType)->paginate();

		return send_response('categories retrieved successfully', 200, $data);
	}

	public function all(Request $request)
	{
		$search = $request->query('search');
		$type = $request->query('type');
		$categories = Category::query();

		if ($search) {
			$categories->where('name', 'like', "%$search%");
		}

		if ($type) {
			$categories->where('type', $type);
		}
		$categories->where('is_active', 1);
		$categories = $categories->orderBy('id', 'desc')->take(20)->get();
		return send_response('categories retrieved successfully', 200, $categories);
	}

	public function trashed(Request $request)
	{
		$categories = Category::query()->onlyTrashed();
		$search = $request->query('search');
		$type = $request->query('type');

		if ($search) {
			$categories->where('name', 'like', "%$search%");
		}

		if ($type) {
			$categories->where('type', $type);
		}

		$data = $categories->paginate();
		return send_response('Trashed Citys retrieved successfully', 200, $data);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:categories',
			'type' => 'required|string|in:expense,revenue',
			'is_active' => 'nullable|boolean',
		]);

		$category = Category::create([
			'name' => $validated['name'],
			'type' => $validated['type'],
			'is_active' => $validated['is_active'],
		]);
		return send_response('Category created successfully', 201, $category);
	}

	public function show($id)
	{
		$category = Category::find($id);
		if (!$category) {
			return send_response('Category not found', 404);
		}
		return send_response('Category retrieved successfully', 200, $category);
	}

	public function update(Request $request, $id)
	{
		$validated = $request->validate([
			'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
			'type' => 'sometimes|string|in:expense,revenue',
			'is_active' => 'sometimes|boolean',
		]);

		$category = Category::find($id);

		if (!$category) {
			return send_response('Category not found', 404);
		}

		$category->update([
			'name' => $validated['name'] ?? $category->name,
			'type' => $validated['type'] ?? $category->type,
			'is_active' => $validated['is_active'] ?? $category->is_active,
		]);

		return send_response('Category updated successfully', 200, $category);
	}

	public function destroy($id)
	{
		$category = Category::find($id);
		if (!$category) {
			return send_response('Category not found', 404);
		}
		$category->delete();
		return send_response('Category deleted successfully', 200);
	}

	public function restore($id)
	{
		$category = Category::withTrashed()->find($id);

		if (!$category) {
			return send_response('Trashed Category not found', 404);
		}

		$category->restore();
		return send_response('Category restored successfully', 200, $category);
	}
}
