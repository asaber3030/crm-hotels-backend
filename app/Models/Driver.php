<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
	use SoftDeletes, HasFactory;

	protected $table = 'drivers';
	protected $fillable = [
		'name',
		'phone',
	];
}
