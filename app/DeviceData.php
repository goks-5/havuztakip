<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeviceData extends Model
{
  public $table = "device_datas";
  public $timestamps = false;
  protected $perPage = 50;

  public static function deleteOldData($day = 180)
  {
    DB::statement('DELETE from device_datas where ISNULL(hourly) and created_at < DATE_SUB(now(), INTERVAL ' . $day . ' DAY) order by created_at limit 10000');
  }
}
