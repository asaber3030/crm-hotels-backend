<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelEmail extends Model
{
	use SoftDeletes;

	protected $table = 'hotel_emails';
	protected $fillable = [
		'hotel_id',
		'email',
	];

	public function hotel()
	{
		return $this->belongsTo(Hotel::class);
	}
}
