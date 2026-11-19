<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 20px 24px; }
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 8px; color: #1e293b; line-height: 1.35; margin: 0; }
        .kop { width: 100%; border-collapse: collapse; border-bottom: 2px solid #015471; margin-bottom: 10px; }
        .kop td { padding-bottom: 7px; vertical-align: middle; }
        .brand { font-size: 13px; font-weight: bold; color: #015471; }
        .sub { font-size: 8px; color: #64748b; }
        .report-title { font-size: 11px; font-weight: bold; color: #015471; text-transform: uppercase; text-align: right; }
        .meta { font-size: 7.5px; color: #475569; text-align: right; margin-top: 2px; }
        .summary-bar { background: #f8fafc; border: 1px solid #cbd5e1; border-left: 3px solid #FF7A3D; padding: 6px 9px; margin-bottom: 9px; font-size: 8px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #015471; color: #ffffff; padding: 5px 6px; font-size: 7.5px; text-transform: uppercase; border: 1px solid #015471; text-align: left; }
        table.data td { border: 1px solid #cbd5e1; padding: 4.5px 6px; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 10px; font-size: 7px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('img/icon/kopswa-icon.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    @endphp
    <table class="kop">
        <tr>
            <td style="width: 55%;">
                <table style="border-collapse: collapse;">
                    <tr>
                        @if($logoBase64)
                            <td style="padding: 0 8px 0 0; vertical-align: middle;">
                                <img src="{{ $logoBase64 }}" alt="Logo" style="height: 28px; width: auto; display: block;">
                            </td>
                        @endif
                        <td style="padding: 0; vertical-align: middle;">
                            <div class="brand">KOPERASI SWADHARMA</div>
                            <div class="sub">Sistem Monitoring &amp; Pengelolaan Aset Sewa Operasional</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%;">
                <div class="report-title">{{ $title }}</div>
                <div class="meta">{{ $subtitle }} &bull; Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    @if(!empty($summaryText))
        <div class="summary-bar">{!! $summaryText !!}</div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th style="width: 24px; text-align: center;">No</th>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $idx => $row)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + 1 }}" style="text-align: center; padding: 14px; color: #64748b;">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Baris: {{ count($rows) }} &bull; Koperasi Swadharma
    </div>
</body>
</html>
