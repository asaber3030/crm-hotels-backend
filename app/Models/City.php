<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
  use SoftDeletes;

  public $timestamps = false;
  protected $table = 'cities';
  protected $fillable = ['name', 'state'];
}
