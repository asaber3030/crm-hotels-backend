<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{

	use SoftDeletes;

	protected $table = 'reservations';
	protected $fillable = [
		'client_id',
		'agent_id',
		'reservation_date',
		'notes'
	];

	public function client()
	{
		return $this->belongsTo(Client::class, 'client_id', 'id');
	}

	public function agent()
	{
		return $this->belongsTo(Agent::class, 'agent_id', 'id');
	}

	public function car_reservations()
	{
		return $this->hasMany(CarReservation::class, 'reservation_id', 'id');
	}
}
