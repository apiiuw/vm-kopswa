<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tax_annual_date' => 'date',
        'tax_5year_date' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function insuranceClaims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getVehicleTypeLabelAttribute(): string
    {
        return $this->vehicle_type === 'honda_crf_150' ? 'Honda CRF 150 L' : 'Honda Supra X 125';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'active' => ['label' => 'Aktif Beroperasi', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'maintenance' => ['label' => 'Dalam Perbaikan / Servis', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'replaced' => ['label' => 'Unit Pengganti Aktif', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'lost' => ['label' => 'Hilang (Proses Polis)', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            'claimed' => ['label' => 'Klaim Asuransi Selesai', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-slate-100 text-slate-800 border-slate-200'],
        };
    }
}
