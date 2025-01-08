<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{

	use SoftDeletes;

	protected $table = 'rooms';
	protected $fillable = ['hotel_id', 'room_type'];

	public function hotel()
	{
		return $this->belongsTo(Hotel::class);
	}
}
