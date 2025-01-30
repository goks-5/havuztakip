<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company'; // Tablonuzun adı
    protected $fillable = ['company_name', 'address', 'telephone'];
    public $timestamps = false;

    public function users()
{
    return $this->hasMany(UserAccount::class, 'company_name', 'company_name');
}

}
