<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AirportReservation extends Model
{

  use SoftDeletes, HasFactory;

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

  protected $hidden = ['deleted_at'];

  public function reservation()
  {
    return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
  }
}
