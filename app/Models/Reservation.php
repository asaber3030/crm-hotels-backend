<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{

	use SoftDeletes, HasFactory;

	protected $table = 'reservations';
	protected $fillable = [
		'client_id',
		'agent_id',
		'reservation_date',
		'notes',
	];
	protected $hidden = ['deleted_at'];

	public function client()
	{
		return $this->belongsTo(Client::class, 'client_id', 'id');
	}

	public function agent()
	{
		return $this->belongsTo(Agent::class, 'agent_id', 'id');
	}

	public function car()
	{
		return $this->hasOne(CarReservation::class, 'reservation_id', 'id');
	}

	public function reservation()
	{
		return $this->hasOne(HotelReservation::class, 'reservation_id', 'id');
	}

	public function airport()
	{
		return $this->hasOne(AirportReservation::class, 'reservation_id', 'id');
	}
}
