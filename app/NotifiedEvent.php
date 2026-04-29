<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifiedEvent extends Model
{
    protected $table = 'notified_events';
    protected $fillable = ['device_id', 'tag_id', 'min_value', 'max_value', 'email', 'status'];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}