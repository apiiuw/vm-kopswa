<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'contract_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'contract_value' => 'decimal:2',
        'main_contract_value' => 'decimal:2',
        'main_monthly_value' => 'decimal:2',
        'additional_contract_value' => 'decimal:2',
        'additional_monthly_value' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function handoverDocuments(): HasMany
    {
        return $this->hasMany(HandoverDocument::class);
    }

    public function insurancePolicies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    public function taxInvoices(): HasMany
    {
        return $this->hasMany(TaxInvoice::class);
    }

    public function getBillingCycleLabelAttribute(): string
    {
        return match ($this->billing_cycle) {
            'triwulanan' => '3 Bulanan (Triwulan)',
            'semesteran' => '6 Bulanan (Semester)',
            'tahunan' => '12 Bulan (Tahunan)',
            '2_tahunan' => '24 Bulan (2 Tahunan)',
            default => 'Bulanan (1 Bulan)',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'qris' => 'QRIS',
            'va' => 'Virtual Account (VA)',
            'transfer' => 'Transfer Bank',
            'giro' => 'Giro / Cek',
            'cash' => 'Tunai (Cash)',
            default => '-',
        };
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
