<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationFactory extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'client_id',
    'agent_id',
    'reservation_date',
    'notes',
  ];

  /**
   * Define the relationship with the Client model.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function client()
  {
    return $this->belongsTo(Client::class);
  }

  /**
   * Define the relationship with the Agent model.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function agent()
  {
    return $this->belongsTo(Agent::class);
  }
}
