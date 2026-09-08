<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $guarded = [];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    protected $casts = [
        'unit_rate_monthly' => 'decimal:2',
        'unit_rate_yearly' => 'decimal:2',
        'total_budget_36m' => 'decimal:2',
    ];
}
