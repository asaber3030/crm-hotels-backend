<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
  use SoftDeletes, HasFactory;

  public $timestamps = false;
  protected $table = 'cities';
  protected $fillable = ['name', 'state'];
  protected $hidden = ['deleted_at'];
}
