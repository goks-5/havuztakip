<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';

    protected $fillable = [
        'offer_id',               // <--- Ekledik
        'kabul_bedeli',
        'caliscak_kisi_sayisi',
        'proje_bitis_tarihi',
    ];

    public $timestamps = false;
}
