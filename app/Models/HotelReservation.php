<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelReservation extends Model
{

	use SoftDeletes, HasFactory;

	protected $fillable = [
		'reservation_id',
		'room_id',
		'hotel_id',
		'city_id',
		'meal_id',
		'company_id',
		'rate_id',
		'payment_type_id',
		'check_in',
		'check_out',
		'rooms_count',
		'status',
		'view',
		'pax_count',
		'adults',
		'children',
		'option_date',
		'confirmation_number',
		'price'
	];


	public function hotel()
	{
		return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
	}

	public function room()
	{
		return $this->belongsTo(Room::class, 'room_id', 'id');
	}

	public function reservation()
	{
		return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
	}

	public function meal()
	{
		return $this->belongsTo(Meal::class, 'meal_id', 'id');
	}

	public function company()
	{
		return $this->belongsTo(Company::class, 'company_id', 'id');
	}

	public function city()
	{
		return $this->belongsTo(City::class, 'city_id', 'id');
	}

	public function rate()
	{
		return $this->belongsTo(Rate::class, 'rate_id', 'id');
	}

	public function payment_type()
	{
		return $this->belongsTo(Rate::class, 'payment_type_id', 'id');
	}
}
