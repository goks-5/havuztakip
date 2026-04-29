<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    public $timestamps = false; // Timestamps sütunlarını devre dışı bırakır

    protected $table = 'offer'; // Tablo adı

    protected $fillable = [
        'offer_no', 'demand_no', 'title', 'delivery_date',
        'company', 'person_name', 'person_email', 'currency',
        'explanation', 'piece', 'unit_price', 'total_price', 
        'total', 'notes', 'created_at'
    ];    
    
    protected $casts = [
        'piece' => 'array',
        'unit_price' => 'array',
        'total_price' => 'array',
        'explanation' => 'array',
    ];
    
    protected $dates = ['created_at'];

    
}
