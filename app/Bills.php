<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    protected $table = 'bills';

    protected $fillable = [
        'project_id',
        'fatura_adi',
        'fatura_numarasi',
        'fatura_bedeli',
        'tedarikci',
        'para_birimi'
    ];

    public $timestamps = false;
}
