<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CustomerMail;
use App\Models\HotelReservation;
use App\Models\Hotel;
use App\Models\Reservation;
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
		try {
			$request->validate([
				'type' => 'required',
				'from' => 'required|email',
				'selected_reservations' => 'required|array',
				'selected_reservations.*' => 'required|string',
				'subject' => 'required',
				'file.*' => 'nullable|file|max:10240',
				'message' => 'required',
			]);

			$selected_reservations = $request->input('selected_reservations');
			$emails = [];
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
				'subject' => $request->input('subject'),
				'message' => $request->input('message'),
				'file' => $request->hasFile('file') ? [
					'name' => $file->getClientOriginalName(),
					'url' => url($file_name),
				] : null,
			];

			foreach ($selected_reservations as $id) {
				$res = HotelReservation::with(['hotel' => fn($q) => $q->with('emails')])->find($id);
				foreach ($res->hotel->emails as $email) {
					$emails[] = $email->email;
				}
				Reservation::where('id', $res->reservation_id)->update(['has_sent_email' => 1]);
			}

			foreach ($emails as $email) {
				Mail::to($email)
					->send(new CustomerMail($data));
			}

			return send_response('Email sent successfully', 200, $data);
		} catch (\Exception $e) {
			return send_response($e->getMessage(), 500);
		}
	}
}
