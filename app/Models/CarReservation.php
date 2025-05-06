<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class CarReservation extends Model
{

	use HasFactory, LogsActivity;

	protected $table = 'car_reservations';
	protected $fillable = [
		'reservation_id',
		'driver_id',
		'airline',
		'meeting_point',
		'arrival_date',
		'arrival_time',
		'status',
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

	public function getActivitylogOptions(): LogOptions
	{
		return LogOptions::defaults()
			->logOnly($this->fillable)
			->useLogName('car_reservation')
			->setDescriptionForEvent(fn(string $eventName) => "CarReservation has been {$eventName}");
	}

	public function activities()
	{
		return $this->morphMany(Activity::class, 'subject');
	}
}
