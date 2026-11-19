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
        'due_date' => 'date',
        'payment_date' => 'date',
        'crf_subtotal' => 'decimal:2',
        'supra_subtotal' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'total_amount_with_ppn' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function taxInvoice()
    {
        return $this->hasOne(TaxInvoice::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        if ($this->status === 'lunas') {
            return 0.0;
        }

        return max(0.0, (float) $this->total_amount_with_ppn - (float) $this->paid_amount);
    }

    public function getPph23AmountAttribute(): float
    {
        // PPh 23 (2% dari Nilai Bruto Sewa)
        return round(((float) $this->gross_amount) * 0.02, 2);
    }

    public function getNetProfitAttribute(): float
    {
        // Penerimaan / Keuntungan Bersih = Bruto - Potongan PPh 23 (2%)
        return round(((float) $this->gross_amount) - $this->pph23_amount, 2);
    }
}
