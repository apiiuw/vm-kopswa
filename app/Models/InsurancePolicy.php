<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vendor_id',
        'policy_number',
        'insurance_company',
        'coverage_type',
        'start_date',
        'end_date',
        'premium_amount',
        'sum_insured',
        'status',
        'policy_file',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium_amount' => 'decimal:2',
        'sum_insured' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getCoverageLabelAttribute(): string
    {
        return match ($this->coverage_type) {
            'all_risk' => 'All Risk / Comprehensive',
            'tlo' => 'TLO (Total Loss Only)',
            'kombinasi' => 'Kombinasi',
            default => ucfirst($this->coverage_type),
        };
    }
}
