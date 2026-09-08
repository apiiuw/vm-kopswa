<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocument extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'stnk' => 'STNK / Pajak Tahunan',
            'pajak_tahunan' => 'PKB & SWDKLLJ Tahunan',
            'plat_5_tahunan' => 'Pajak & Ganti Plat 5 Tahunan',
            'bpkb' => 'BPKB',
            'asuransi' => 'Polis Asuransi Kendaraan',
            'blokir_polda' => 'Surat Blokir STNK Polda',
            default => 'Dokumen Lainnya',
        };
    }
}
