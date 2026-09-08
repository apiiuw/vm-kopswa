<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'service_berkala' => 'Service Berkala & Pelumas',
            'ganti_ban' => 'Penggantian Ban',
            'ganti_accu' => 'Penggantian Accu / Aki',
            'sparepart' => 'Penggantian Sparepart',
            'motor_pengganti' => 'Biaya Sewa Motor Pengganti',
            default => 'Lainnya',
        };
    }
}
