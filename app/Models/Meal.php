<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model
{

	use SoftDeletes;

	protected $table = 'meals';
	protected $fillable = ['meal_type', 'state'];
}
