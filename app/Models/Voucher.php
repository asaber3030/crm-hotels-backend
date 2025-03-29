<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
	protected $table = 'vouchers';
	protected $fillable = [
		'company_id',
		'city_id',
		'hotel_id',
		'meal_id',
		'room_id',
		'view',
		'adults',
		'children',
		'internal_confirmation',
		'nationality',
		'client_name',
		'hcn',
		'pax',
		'rooms_count',
		'notes',
		'check_in',
		'check_out',
	];

	public function company()
	{
		return $this->belongsTo(Company::class, 'company_id', 'id');
	}

	public function city()
	{
		return $this->belongsTo(City::class, 'city_id', 'id');
	}

	public function hotel()
	{
		return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
	}

	public function meal()
	{
		return $this->belongsTo(Meal::class, 'meal_id', 'id');
	}

	public function room()
	{
		return $this->belongsTo(Room::class, 'room_id', 'id');
	}
}
