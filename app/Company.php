<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies'; // Tablonuzun adı

    public function users()
{
    return $this->hasMany(UserAccount::class, 'company_name', 'company_name');
}

}
