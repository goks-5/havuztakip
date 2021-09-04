<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Spatial;

class CompanySetting extends Model
{
public $table = "company_settings";
public $primaryKey = "company_id"; 
}
