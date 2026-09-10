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
        'proof_files' => 'array',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getTypeLabelAttribute(): string
    {
        $complaints = $this->parsed_complaints;
        if (!empty($complaints) && count($complaints) > 1) {
            $cats = [];
            foreach ($complaints as $c) {
                $raw = $c['category'] ?? '';
                if (str_contains(strtolower($raw), 'aki') || str_contains(strtolower($raw), 'accu')) $cats['accu'] = 'Accu/Aki';
                elseif (str_contains(strtolower($raw), 'ban')) $cats['ban'] = 'Ban';
                elseif (str_contains(strtolower($raw), 'service') || str_contains(strtolower($raw), 'pelumas') || str_contains(strtolower($raw), 'oli')) $cats['service'] = 'Servis/Oli';
                else $cats['sparepart'] = 'Sparepart';
            }
            if (!empty($cats)) {
                return implode(' & ', array_values($cats));
            }
        }

        return match($this->type) {
            'service_berkala' => 'Service Berkala & Pelumas',
            'ganti_ban' => 'Penggantian Ban',
            'ganti_accu' => 'Penggantian Accu / Aki',
            'sparepart' => 'Penggantian Sparepart',
            'motor_pengganti' => 'Biaya Sewa Motor Pengganti',
            default => 'Lainnya',
        };
    }

    public function getParsedComplaintsAttribute(): array
    {
        $normalized = str_replace("\r\n", "\n", $this->description ?? '');
        $lines = explode("\n", $normalized);
        $blocks = [];
        $current = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            if (preg_match('/^\[(?:Item|Keluhan) #(\d+): (.*?)\]$/', $trimmed, $m)) {
                if ($current) $blocks[] = $current;
                $current = [
                    'index' => (int) $m[1],
                    'category' => $m[2],
                    'title' => '',
                    'cost' => '',
                    'description' => '',
                ];
            } elseif ($current) {
                if (str_starts_with($trimmed, 'Judul:')) {
                    $raw = trim(substr($trimmed, 6));
                    if (preg_match('/^(.*?)(?:\s*\((?:Biaya|Estimasi):\s*Rp\s*([\d\.,]+)\))?$/i', $raw, $tm)) {
                        $current['title'] = trim($tm[1]);
                        $current['cost'] = trim($tm[2] ?? '');
                    } else {
                        $current['title'] = $raw;
                    }
                } elseif (str_starts_with($trimmed, 'Uraian:')) {
                    $current['description'] = trim(substr($trimmed, 7));
                } else {
                    $current['description'] .= ($current['description'] ? ' ' : '') . $trimmed;
                }
            }
        }
        if ($current) $blocks[] = $current;

        return $blocks;
    }

    public function getCleanDescriptionAttribute(): string
    {
        $normalized = str_replace("\r\n", "\n", $this->description ?? '');
        $lines = explode("\n", $normalized);
        $clean = [];
        $inComplaint = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']') && !str_starts_with($trimmed, '[Item #') && !str_starts_with($trimmed, '[Keluhan #')) {
                continue;
            }
            if (str_starts_with($trimmed, '[Item #') || str_starts_with($trimmed, '[Keluhan #')) {
                $inComplaint = true;
                continue;
            }
            if ($inComplaint) {
                if (str_starts_with($trimmed, 'Judul:') || str_starts_with($trimmed, 'Uraian:')) {
                    continue;
                }
            }
            $clean[] = $line;
        }

        return trim(implode(' ', $clean));
    }
}
