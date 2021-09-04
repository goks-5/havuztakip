<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DeviceData extends Model
{
        public $table = "device_datas";
          public $timestamps = false;
          protected $perPage = 50;
}
