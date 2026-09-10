<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'proof_files' => 'array',
        'resolved_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return match($this->request_type) {
            'perbaikan_servis' => 'Pengajuan Perbaikan / Servis',
            'ganti_ban_accu' => 'Penggantian Ban / Accu',
            'unit_pengganti' => 'Pengajuan Unit Pengganti Sementara',
            'lapor_kehilangan' => 'Laporan Insiden / Kehilangan (TLO)',
            default => 'Layanan Klien',
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'menunggu_review' => [
                'label' => 'Menunggu Review',
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
            ],
            'diproses' => [
                'label' => 'Sedang Diproses',
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dot' => 'bg-blue-500 animate-pulse',
            ],
            'unit_dikirim' => [
                'label' => 'Unit Dikirim',
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'dot' => 'bg-purple-500',
            ],
            'selesai' => [
                'label' => 'Selesai & Disetujui',
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500',
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => 'bg-slate-50 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-500',
            ],
        };
    }
}
