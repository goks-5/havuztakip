<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Fault extends Model
{
    protected $casts = [
        'accepted_at' => 'date',
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Belirli bir tarihten eski olanları otomatik olarak filtrele
        $this->filterOldRecords();
    }

    private function filterOldRecords()
    {
        $thresholdDate = Carbon::now()->subMonths(13);

    
        $this->where('created_at', '>=', $thresholdDate);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'maintainer_id');
    }
}
