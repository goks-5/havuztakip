<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
  use SoftDeletes;
  protected $dates = ['deleted_at'];
}
