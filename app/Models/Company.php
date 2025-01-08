<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
	use SoftDeletes, HasFactory;

	protected $table = 'companies';
	protected $fillable = [
		'name',
		'state',
	];
}
