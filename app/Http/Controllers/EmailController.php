<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CustomerMail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

	public function test_email(Request $request)
	{
		$request->validate([
			'file.*' => 'required|file|max:10240',
		]);

		$uploads = [];

		foreach ($request->file('file') as $file) {
			$unique_file_name =  time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
			$file_name = '/uploads/attachments/' . $unique_file_name;
			$file->move('uploads/attachments', $unique_file_name);

			$uploads[] = $file_name;
		}

		return send_response('File uploaded successfully', 200, $uploads);
	}

	public function send_email(Request $request)
	{
		$request->validate([
			'type' => 'required',
			'from' => 'required|email',
			'to' => 'required|email',
			'cc' => 'nullable|array',
			'cc.*' => 'email',
			'subject' => 'required',
			'file.*' => 'nullable|file|max:10240',
			'message' => 'required',
		]);

		$file_name = '';

		if ($request->hasFile('file')) {
			$file = $request->file('file');
			$unique_file_name =  time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
			$file_name = '/uploads/attachments/' . $unique_file_name;
			$file->move('uploads/attachments', $unique_file_name);
		}

		$data = [
			'email_type' => $request->input('type'),
			'from' => $request->input('from'),
			'to' => $request->input('to'),
			'cc' => $request->input('cc'),
			'subject' => $request->input('subject'),
			'message' => $request->input('message'),
			'file' => $request->hasFile('file') ? [
				'name' => $file->getClientOriginalName(),
				'url' => url($file_name),
			] : null,
		];

		Mail::to($data['to'])
			->cc($data['cc'])
			->send(new CustomerMail($data));

		return send_response('Email sent successfully', 200, $data);
	}
}
