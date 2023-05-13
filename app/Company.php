<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Spatial;

class Company extends Model
{
    
    public function users(){ 
        return $this->belongsToMany(User::class,'company_users');
    }

}
