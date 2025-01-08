<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{

	use SoftDeletes, HasFactory;

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
