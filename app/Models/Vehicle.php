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
        'acquisition_date' => 'date',
        'sold_date' => 'date',
        'rental_price_per_month' => 'decimal:2',
        'total_contract_value' => 'decimal:2',
        'additional_contract_value' => 'decimal:2',
        'additional_monthly_value' => 'decimal:2',
        'invoice_value' => 'decimal:2',
        'acquisition_value' => 'decimal:2',
        'ppn_pm' => 'decimal:2',
        'dealer_discount' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'residual_percentage' => 'decimal:2',
        'sold_price' => 'decimal:2',
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

    public function handoverDocuments(): HasMany
    {
        return $this->hasMany(HandoverDocument::class);
    }

    public function insurancePolicies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    public function getAssetTypeLabelAttribute(): string
    {
        return match ($this->asset_type) {
            'mobil' => 'Mobil',
            'motor' => 'Motor',
            'fotocopy' => 'Mesin Fotocopy',
            'dispenser' => 'Dispenser',
            default => ucfirst($this->asset_type ?: 'Motor'),
        };
    }

    public function getVehicleTypeLabelAttribute(): string
    {
        if (in_array($this->asset_type, ['fotocopy', 'dispenser'], true)) {
            return $this->asset_type_label.' - '.($this->model_name ?: $this->brand);
        }

        return match ($this->vehicle_type) {
            'honda_crf_150' => 'Honda CRF 150 L',
            'honda_supra_x_125' => 'Honda Supra X 125',
            default => trim(($this->brand ? $this->brand.' ' : '').($this->model_name ?: $this->vehicle_type)),
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'active' => ['label' => 'Aktif Disewa', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'standby' => ['label' => 'Aktif Standby', 'class' => 'bg-sky-100 text-sky-800 border-sky-200'],
            'maintenance' => ['label' => 'Dalam Perbaikan / Servis', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'replaced' => ['label' => 'Unit Pengganti Aktif', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'sold' => ['label' => 'Dijual / Terjual', 'class' => 'bg-slate-200 text-slate-800 border-slate-300'],
            'lost' => ['label' => 'Hilang (Proses Polis)', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            'claimed' => ['label' => 'Klaim Asuransi Selesai', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-slate-100 text-slate-800 border-slate-200'],
        };
    }

    public function getMonthlyDepreciationAttribute(): float
    {
        $acquisition = (float) ($this->acquisition_value ?: $this->invoice_value ?: 0);
        $residual = (float) ($this->residual_value ?? 0);
        $life = max(1, (int) ($this->useful_life_months ?: 48));

        if ($acquisition <= $residual) {
            return 0.0;
        }

        return round(($acquisition - $residual) / $life, 2);
    }

    public function getElapsedDepreciationMonthsAttribute(): int
    {
        if (! $this->acquisition_date) {
            return 0;
        }

        $endDate = $this->sold_date && $this->status === 'sold' ? $this->sold_date : now();
        $months = (int) $this->acquisition_date->diffInMonths($endDate);
        $life = max(1, (int) ($this->useful_life_months ?: 48));

        return max(0, min($life, $months));
    }

    public function getAccumulatedDepreciationAttribute(): float
    {
        return round($this->monthly_depreciation * $this->elapsed_depreciation_months, 2);
    }

    public function getBookValueAttribute(): float
    {
        $acquisition = (float) ($this->acquisition_value ?: $this->invoice_value ?: 0);
        $residual = (float) ($this->residual_value ?? 0);

        return max($residual, round($acquisition - $this->accumulated_depreciation, 2));
    }

    public static function assetCodePrefix(string $assetType): string
    {
        return match ($assetType) {
            'mobil' => 'KOPSWA-MBL-',
            'fotocopy' => 'KOPSWA-FC-',
            'dispenser' => 'KOPSWA-DSP-',
            default => 'KOPSWA-MTR-',
        };
    }

    public static function generateNextAssetCode(string $assetType): string
    {
        $prefix = self::assetCodePrefix($assetType);

        $existingCodes = self::where('asset_type', $assetType)
            ->whereNotNull('asset_code')
            ->where('asset_code', 'like', $prefix.'%')
            ->pluck('asset_code');

        $maxNum = 0;
        foreach ($existingCodes as $code) {
            if (preg_match('/(\d+)$/', (string) $code, $m)) {
                $maxNum = max($maxNum, (int) $m[1]);
            }
        }

        $countOfType = self::where('asset_type', $assetType)->count();
        $nextNum = max($maxNum, $countOfType) + 1;

        return $prefix.str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);
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
