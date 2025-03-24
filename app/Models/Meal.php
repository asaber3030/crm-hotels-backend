<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meal extends Model
{

	use SoftDeletes, HasFactory;

	protected $table = 'meals';
	protected $fillable = ['meal_type', 'state'];
}
