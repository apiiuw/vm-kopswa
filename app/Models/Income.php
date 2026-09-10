<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $guarded = [];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    protected $casts = [
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'crf_subtotal' => 'decimal:2',
        'supra_subtotal' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'total_amount_with_ppn' => 'decimal:2',
    ];

    public function getPph23AmountAttribute(): float
    {
        // PPh 23 (2% dari Nilai Bruto Sewa)
        return round(((float) $this->gross_amount) * 0.02, 2);
    }
}
