<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fault extends Model
{
    protected $fillable = [
        'equipment_id',
        'fault_type',
        'fault_code',
        'fault_comment',
        'reporting_user',
        'status',
        'company_id'
    ];

    protected $casts = [
        'accepted_at' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'maintainer_id');
    }
}
