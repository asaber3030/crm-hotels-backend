<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class AirportReservation extends Model
{

  use SoftDeletes, HasFactory, LogsActivity;

  protected $table = 'airport_reservations';
  protected $fillable = [
    'reservation_id',
    'airport_name',
    'airline',
    'runner',
    'price',
    'flight_number',
    'coming_from',
    'passenger_count',
    'status',
    'arrival_date',
    'arrival_time',
    'persons_count',
    'statment',
  ];

  public function reservation()
  {
    return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
  }

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly($this->fillable)
      ->useLogName('airport_reservation')
      ->setDescriptionForEvent(fn(string $eventName) => "AirportReservation has been {$eventName}");
  }

  public function activities()
  {
    return $this->morphMany(Activity::class, 'subject');
  }
}
