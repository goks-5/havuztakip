<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
   protected $table = 'company'; // Veritabanındaki tablo adı

    protected $fillable = [
        'company_name',
        'address',
        'telephone',
    ];

    public function users()
{
    return $this->hasMany(UserAccount::class, 'company_name', 'company_name');
}

}
