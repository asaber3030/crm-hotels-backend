<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{

	use SoftDeletes;

	protected $table = 'payments_types';
	protected $fillable = [
		'name',
		'state'
	];
	protected $hidden = ['deleted_at'];
}
