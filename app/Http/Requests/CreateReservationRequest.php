<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'client.client_id' => 'required_without_all:client.name,client.email,client.phone,client.nationality|sometimes|integer|exists:clients,id',
			'client.name' => 'required_without:client_id|sometimes|string|max:255',
			'client.email' => 'required_without:client_id|sometimes|email|max:255',
			'client.phone' => 'required_without:client_id|sometimes|string|max:20',
			'client.nationality' => 'required_without:client_id|sometimes|string|max:100',

			// Hotel Reservation Validation
			'hotel.hotel_id' => 'required|integer|exists:hotels,id',
			'hotel.city_id' => 'required|integer|exists:cities,id',
			'hotel.payment_type_id' => 'required|integer|exists:payments_types,id',
			'hotel.meal_id' => 'required|integer|exists:meals,id',
			'hotel.company_id' => 'required|integer|exists:companies,id',
			'hotel.rate_id' => 'required|integer|exists:rates,id',
			'hotel.check_in' => 'required|date',
			'hotel.check_out' => 'required|date',
			'hotel.rooms_count' => 'required|integer|min:1',
			'hotel.view' => 'required|string|max:255',
			'hotel.pax_count' => 'required|integer|min:1',
			'hotel.adults' => 'required|integer|min:0',
			'hotel.children' => 'required|integer|min:0',
			'hotel.option_date' => 'required|date',
			'hotel.status' => 'required|in:new,in_revision,confirmed,cancelled,refunded,guaranteed',
			'hotel.confirmation_number' => 'required|string|max:50',
			'hotel.price' => 'required|numeric|min:0',

			// Airport Reservation (Optional)
			'airport.email' => 'sometimes|required|email|max:255',
			'airport.phone' => 'sometimes|required|string|max:20',
			'airport.nationality' => 'sometimes|required|string|max:100',
			'airport.airport_name' => 'sometimes|required|string|max:255',
			'airport.airline' => 'sometimes|required|string|max:255',
			'airport.runner' => 'sometimes|required|string|max:255',
			'airport.flight_number' => 'sometimes|required|string|max:50',
			'airport.coming_from' => 'sometimes|required|string|max:255',
			'airport.passenger_count' => 'sometimes|required|integer|min:0',
			'airport.status' => 'sometimes|required|in:pending,done,cancelled',
			'airport.persons_count' => 'sometimes|required|integer|min:0',
			'airport.statment' => 'sometimes|required|integer|min:0',
			'airport.price' => 'sometimes|required|numeric|min:0',
			'airport.arrival_date' => 'sometimes|required|date',
			'airport.arrival_time' => ['sometimes', 'required'],

			// Car Reservation (Optional)
			'car.email' => 'sometimes|required|email|max:255',
			'car.phone' => 'sometimes|required|string|max:20',
			'car.nationality' => 'sometimes|required|string|max:100',
			'car.driver_id' => 'sometimes|required|integer|min:0|exists:drivers,id',
			'car.airline' => 'sometimes|required|string|max:255',
			'car.meeting_point' => 'sometimes|required|string|max:255',
			'car.coming_from' => 'sometimes|required|string|max:255',
			'car.status' => 'sometimes|required|in:pending,done,cancelled',
			'car.price' => 'sometimes|required|numeric|min:0',
			'car.arrival_date' => 'sometimes|required|date',
			'car.arrival_time' => ['sometimes', 'required'],
			'car.comments' => 'nullable|string|max:1000',
		];
	}

	public function messages(): array
	{
		return [
			'client.name.required' => 'Client Name is required.',
			'client.email.required' => 'A valid email is required.',
			'client.phone.required' => 'Phone number is required.',
			'client.nationality.required' => 'Nationality is required.',

			'hotel.hotel_id.exists' => 'Invalid hotel selected.',
			'hotel.city_id.exists' => 'Invalid city selected.',
			'hotel.check_out.after' => 'Check-out must be after check-in.',

			'airport.arrival_time.regex' => 'Invalid time format, use HH:MM.',
			'car.arrival_time.regex' => 'Invalid time format, use HH:MM.',
		];
	}
}
