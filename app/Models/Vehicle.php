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

    public function setUserHolderAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['user_holder'] = null;

            return;
        }

        $cleaned = trim(preg_replace('/\s+/', ' ', (string) $value));
        $cleaned = preg_replace('/,([^\s])/', ', ${1}', $cleaned);
        $words = explode(' ', $cleaned);
        $formatted = [];

        foreach ($words as $w) {
            if (empty($w)) {
                continue;
            }
            if (preg_match('/^S\.(T|TP|PD|E|KOM|H|SOS|SI|PT)\.?$/i', $w)) {
                $formatted[] = strtoupper($w);
            } else {
                $formatted[] = mb_convert_case(mb_strtolower($w), MB_CASE_TITLE, 'UTF-8');
            }
        }

        $this->attributes['user_holder'] = implode(' ', $formatted);
    }

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

    public function latestMaintenanceRecord()
    {
        return $this->hasOne(MaintenanceRecord::class)->ofMany([
            'date' => 'max',
            'id' => 'max',
        ]);
    }

    public function getLatestServiceKmAttribute(): ?int
    {
        if ($this->relationLoaded('maintenanceRecords')) {
            $record = $this->maintenanceRecords->whereNotNull('odometer_km')->sortByDesc('date')->first();

            return $record ? (int) $record->odometer_km : null;
        }

        return $this->maintenanceRecords()->whereNotNull('odometer_km')->orderByDesc('date')->orderByDesc('id')->value('odometer_km');
    }

    public function getLatestServiceRecordAttribute(): ?MaintenanceRecord
    {
        if ($this->relationLoaded('maintenanceRecords')) {
            return $this->maintenanceRecords->whereNotNull('odometer_km')->sortByDesc('date')->first()
                ?? $this->maintenanceRecords->sortByDesc('date')->first();
        }

        return $this->maintenanceRecords()->whereNotNull('odometer_km')->orderByDesc('date')->orderByDesc('id')->first();
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
        return match ($this->status) {
            'active' => ['label' => 'Aktif Beroperasi', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'maintenance' => ['label' => 'Dalam Perbaikan / Servis', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'replaced' => ['label' => 'Unit Pengganti Aktif', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'lost' => ['label' => 'Hilang (Proses Polis)', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            'claimed' => ['label' => 'Klaim Asuransi Selesai', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-slate-100 text-slate-800 border-slate-200'],
        };
    }

    public function getIconNameAttribute(): string
    {
        $cat = strtolower($this->category ?? '');
        $type = strtolower($this->type_name ?? '');
        $model = strtolower($this->model_name ?? '');
        $group = strtolower($this->vehicle_type ?? '');

        // 1. Truk / Bus / Alat Berat / Double Cabin (Mobil Besar / Truk)
        if (
            str_contains($cat, 'alat berat') ||
            str_contains($cat, 'truk') ||
            str_contains($type, 'truk') ||
            str_contains($type, 'alat berat') ||
            str_contains($type, 'traktor') ||
            str_contains($type, 'bus') ||
            str_contains($type, 'double cabin') ||
            str_contains($model, 'truk') ||
            str_contains($model, 'fuso') ||
            str_contains($model, 'hino') ||
            str_contains($model, 'canter') ||
            str_contains($model, 'hilux') ||
            str_contains($model, 'd-max') ||
            str_contains($model, 'triton')
        ) {
            return 'truck';
        }

        // 2. Mobil / Pick Up / Minibus / Sedan (Mobil Biasa / Roda 4)
        if (
            str_contains($cat, 'roda 4') ||
            str_contains($cat, 'mobil') ||
            str_contains($type, 'mobil') ||
            str_contains($type, 'pick up') ||
            str_contains($type, 'minibus') ||
            str_contains($type, 'mpv') ||
            str_contains($type, 'sedan') ||
            str_contains($type, 'suv') ||
            str_contains($model, 'avanza') ||
            str_contains($model, 'xenia') ||
            str_contains($model, 'innova') ||
            str_contains($model, 'gran max') ||
            str_contains($model, 'carry')
        ) {
            return 'car';
        }

        // 3. Sepeda Motor (Roda 2 / Trail / Bebek / Matic / CRF / Supra / KLX)
        return 'bike';
    }
}
