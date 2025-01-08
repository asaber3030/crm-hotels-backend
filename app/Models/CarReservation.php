<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarReservation extends Model
{
	protected $table = 'car_reservations';
	protected $fillable = [
		'reservation_id',
		'driver_id',
		'airline',
		'meeting_point',
		'arrival_date',
		'arrival_time',
		'coming_from',
		'comments',
		'price'
	];

	public function driver()
	{
		return $this->belongsTo(Driver::class, 'driver_id', 'id');
	}

	public function reservation()
	{
		return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
	}
}
