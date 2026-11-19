<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxInvoice extends Model
{
    protected $fillable = [
        'income_id',
        'vendor_id',
        'faktur_number',
        'transaction_code',
        'tax_period',
        'faktur_date',
        'buyer_name',
        'buyer_npwp',
        'buyer_nitku',
        'buyer_address',
        'dpp_amount',
        'ppn_rate',
        'ppn_amount',
        'pph_amount',
        'coretax_status',
        'coretax_reference',
        'notes',
    ];

    protected $casts = [
        'faktur_date' => 'date',
        'dpp_amount' => 'decimal:2',
        'ppn_rate' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'pph_amount' => 'decimal:2',
    ];

    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
