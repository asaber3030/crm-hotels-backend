<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
	use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

	protected $table = 'agents';
	protected $fillable = ['name', 'email', 'password', 'contact_number', 'address', 'role', 'state'];

	protected $hidden = ['password'];
}
