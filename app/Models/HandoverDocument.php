<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoverDocument extends Model
{
    protected $fillable = [
        'bast_number',
        'bast_type',
        'vendor_id',
        'vehicle_id',
        'replacement_vehicle_id',
        'handover_date',
        'contract_start_date',
        'contract_end_date',
        'handed_over_by',
        'received_by',
        'recipient_position',
        'odometer_km',
        'replacement_cost',
        'condition_notes',
        'signed_file',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'replacement_cost' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function replacementVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'replacement_vehicle_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->bast_type) {
            'unit_baru' => 'BAST Unit Baru',
            'unit_pengganti' => 'BAST Unit Pengganti',
            'pengembalian' => 'BAST Pengembalian Unit',
            default => ucfirst(str_replace('_', ' ', $this->bast_type)),
        };
    }
}
