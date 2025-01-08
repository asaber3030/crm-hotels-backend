<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{

	use SoftDeletes;

	protected $table = 'hotels';
	protected $fillable = [
		'name',
		'city_id',
		'phone_number',
		'email',
	];

	public function city()
	{
		return $this->belongsTo(City::class);
	}
}
