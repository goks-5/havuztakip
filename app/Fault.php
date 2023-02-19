<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fault extends Model
{
    

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'maintainer_id');
    }
}
