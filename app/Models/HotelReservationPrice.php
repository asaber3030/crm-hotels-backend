<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelReservationPrice extends Model
{
	protected $table = 'hotel_reservations_prices';
	protected $fillable = [
		'hotel_reservation_id',
		'price',
		'day_number'
	];
}
