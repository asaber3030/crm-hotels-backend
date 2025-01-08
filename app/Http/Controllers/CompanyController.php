<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
  public function index()
  {
    $companies = Company::orderBy('id', 'desc')->paginate();
    return send_response('Companies retrieved successfully', 200, $companies);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'state' => 'required|string|in:pending,rejected,approved',
    ]);
    $company = Company::create($request->only(['name', 'state']));
    return send_response('Company created successfully', 201, $company);
  }

  public function show($id)
  {
    $company = Company::find($id);
    if (!$company) {
      return send_response('Company not found', 404);
    }
    return send_response('Company retrieved successfully', 200, $company);
  }

  public function update(Request $request, $id)
  {
    $company = Company::find($id);
    if (!$company) {
      return send_response('Company not found', 404);
    }
    $request->validate([
      'name' => 'sometimes|string|max:255',
      'state' => 'sometimes|string|in:pending,rejected,approved',
    ]);

    $company->update($request->only(['name', 'state']));
    return send_response('Company updated successfully', 200, $company);
  }

  public function destroy($id)
  {
    $company = Company::find($id);
    if (!$company) {
      return send_response('Company not found', 404);
    }
    $company->delete();
    return send_response('Company deleted successfully', 200);
  }

  public function trashed()
  {
    $deletedCompanies = Company::onlyTrashed()->paginate();
    return send_response('Deleted companies retrieved successfully', 200, $deletedCompanies);
  }

  public function restore($id)
  {
    $company = Company::onlyTrashed()->find($id);
    if (!$company) {
      return send_response('Deleted company not found', 404);
    }
    $company->restore();
    return send_response('Company restored successfully', 200, $company);
  }
}
