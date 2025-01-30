<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    // Tablonun adı
    protected $table = 'user'; // Tablonuzun adı "user" olarak görünüyor

    // Toplu atamaya izin verilen sütunlar
    protected $fillable = ['user_name', 'company_name', 'email', 'telephone'];

    // Timestamps özelliği kapalı
    public $timestamps = false;

    /**
     * Kullanıcı adını büyük harfle döndürmek için bir örnek accessor.
     */
    public function getUserNameAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Varsayılan bir telefon numarası döndürmek için accessor.
     */
    public function getTelephoneAttribute($value)
    {
        return $value ?: 'Telefon bilgisi yok';
    }

    /**
     * Kullanıcı adını ve şirket adını birleştirerek döndürmek için özel bir metot.
     */
    public function getFullInfo()
    {
        return "{$this->user_name} ({$this->company_name})";
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_name', 'company_name');
    }

}
