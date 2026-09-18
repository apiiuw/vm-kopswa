<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $income->invoice_number }} - PT Swadharma Utama Prima</title>
    <style>
        @page {
            margin: 28px 32px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #015471;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .company-name {
            font-size: 15px;
            font-weight: 800;
            color: #015471;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-tagline {
            font-size: 10px;
            color: #FF7A3D;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .company-address {
            font-size: 9px;
            color: #64748b;
            line-height: 1.3;
        }
        .invoice-badge-title {
            font-size: 20px;
            font-weight: 900;
            color: #015471;
            text-align: right;
            margin: 0;
            letter-spacing: 1px;
        }
        .invoice-badge-num {
            font-size: 11px;
            font-weight: bold;
            color: #FF7A3D;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-lunas {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .status-tertagih {
            background-color: #ffedd5;
            color: #9a3412;
            border: 1px solid #fdba74;
        }
        .status-draft {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 18px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .meta-title {
            font-size: 9px;
            font-weight: bold;
            color: #015471;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background-color: #015471;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-box {
            width: 48%;
            float: right;
            border: 1.5px solid #015471;
            border-radius: 8px;
            overflow: hidden;
        }
        .summary-row {
            padding: 6px 12px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-total {
            background-color: #015471;
            color: #ffffff;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .payment-box {
            width: 48%;
            float: left;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 9.5px;
        }
        .clear {
            clear: both;
        }
        .signatures-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sig-role {
            font-size: 10px;
            color: #475569;
            font-weight: bold;
        }
        .sig-name {
            font-size: 11px;
            font-weight: 800;
            color: #015471;
            text-decoration: underline;
            margin-top: 55px;
        }
        .sig-title {
            font-size: 9px;
            color: #64748b;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
    </style>
</head>
<body>

    <!-- Header Perusahaan -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <div class="company-name">PT Swadharma Utama Prima</div>
                <div class="company-tagline">Solusi Monitoring & Manajemen Armada Kendaraan Operasional</div>
                <div class="company-address">
                    Gedung Menara Prima, Jl. DR. Ide Anak Agung Gde Agung, Kuningan, Jakarta Selatan<br>
                    Telp: (021) 5290-0000 | Email: invoice@ptsup.co.id | Web: https://ptsup.co.id
                </div>
            </td>
            <td style="width: 42%; vertical-align: top; text-align: right;">
                <div class="invoice-badge-title">FAKTUR TAGIHAN</div>
                <div class="invoice-badge-num">{{ $income->invoice_number }}</div>
                <div>
                    @if($income->status === 'lunas')
                        <span class="status-pill status-lunas">LUNAS / PAID</span>
                    @elseif($income->status === 'tertagih')
                        <span class="status-pill status-tertagih">TERTAGIH / UNPAID</span>
                    @else
                        <span class="status-pill status-draft">DRAFT INVOICE</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Metadata Invoice & Rekanan -->
    <table class="meta-table" cellpadding="0" cellspacing="6">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="meta-box">
                    <div class="meta-title">DITUJUKAN KEPADA (KLIEN / MITRA):</div>
                    <div style="font-size: 12px; font-weight: 800; color: #015471; margin-bottom: 2px;">
                        {{ $income->client_name }}
                    </div>
                    @if($income->vendor)
                        <div style="font-size: 10px; font-weight: bold; color: #334155;">
                            {{ $income->vendor->name }} ({{ $income->vendor->code }})
                        </div>
                        @if($income->vendor->contract_number)
                            <div style="font-size: 9.5px; color: #64748b;">
                                No. Kontrak: <strong>{{ $income->vendor->contract_number }}</strong>
                            </div>
                        @endif
                        @if($income->vendor->pic_name)
                            <div style="font-size: 9px; color: #64748b;">
                                Up: {{ $income->vendor->pic_name }} ({{ $income->vendor->phone ?: '-' }})
                            </div>
                        @endif
                    @endif
                    @if($income->bast_number)
                        <div style="font-size: 9.5px; color: #015471; margin-top: 3px;">
                            No. BAST: <strong class="font-mono">{{ $income->bast_number }}</strong>
                        </div>
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="meta-box">
                    <div class="meta-title">RINCIAN PENERBITAN FAKTUR:</div>
                    <table style="width: 100%; border: none; font-size: 10px;" cellpadding="1" cellspacing="0">
                        <tr>
                            <td style="width: 42%; color: #64748b; border: none;">Tanggal Invoice:</td>
                            <td style="border: none; font-weight: bold;">{{ $income->invoice_date ? $income->invoice_date->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; border: none;">Periode Sewa:</td>
                            <td style="border: none; font-weight: 800; color: #015471;">{{ $income->period_month_year }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; border: none;">Jatuh Tempo Bayar:</td>
                            <td style="border: none; font-weight: bold; color: #FF7A3D;">
                                {{ $income->invoice_date ? $income->invoice_date->addDays(30)->translatedFormat('d F Y') : '30 Hari Sejak Terbit' }}
                            </td>
                        </tr>
                        @if($income->payment_date)
                            <tr>
                                <td style="color: #166534; border: none;">Tanggal Lunas:</td>
                                <td style="border: none; font-weight: bold; color: #166534;">{{ $income->payment_date->translatedFormat('d F Y') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Rincian Item Tagihan Sewa -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;" class="text-center">No</th>
                <th style="width: 44%;">Deskripsi Layanan Sewa Kendaraan</th>
                <th style="width: 12%;" class="text-center">Jumlah</th>
                <th style="width: 18%;" class="text-right">Tarif / Unit (Rp)</th>
                <th style="width: 20%;" class="text-right">Subtotal DPP (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @if($income->crf_count > 0)
                <tr>
                    <td class="text-center font-bold">{{ $no++ }}</td>
                    <td>
                        <strong>Sewa Sepeda Motor Trail - Honda CRF 150 L</strong><br>
                        <span style="font-size: 8.5px; color: #64748b;">Operasional Kebun Lapangan • Periode: {{ $income->period_month_year }}</span>
                    </td>
                    <td class="text-center font-bold font-mono">{{ $income->crf_count }} Unit</td>
                    <td class="text-right font-mono">Rp 1.570.000</td>
                    <td class="text-right font-bold font-mono">Rp {{ number_format($income->crf_subtotal, 0, ',', '.') }}</td>
                </tr>
            @endif

            @if($income->supra_count > 0)
                <tr>
                    <td class="text-center font-bold">{{ $no++ }}</td>
                    <td>
                        <strong>Sewa Sepeda Motor Bebek - Honda Supra X 125</strong><br>
                        <span style="font-size: 8.5px; color: #64748b;">Operasional Administrasi & Logistik Kebun • Periode: {{ $income->period_month_year }}</span>
                    </td>
                    <td class="text-center font-bold font-mono">{{ $income->supra_count }} Unit</td>
                    <td class="text-right font-mono">Rp 860.000</td>
                    <td class="text-right font-bold font-mono">Rp {{ number_format($income->supra_subtotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Payment Instructions & Summary -->
    <div class="summary-table">
        <div class="payment-box">
            <div style="font-weight: 800; color: #015471; margin-bottom: 4px; text-transform: uppercase; font-size: 9.5px;">
                Petunjuk Pembayaran Transfer Bank:
            </div>
            <div>Harap menyetorkan pembayaran sesuai nominal tagihan ke rekening resmi penyedia:</div>
            <table style="width: 100%; border: none; margin: 6px 0; font-size: 9.5px;" cellpadding="1" cellspacing="0">
                <tr>
                    <td style="width: 32%; color: #64748b; border: none;">Nama Bank:</td>
                    <td style="border: none; font-weight: bold;">Bank Mandiri (Persero) Tbk</td>
                </tr>
                <tr>
                    <td style="color: #64748b; border: none;">Nomor Rekening:</td>
                    <td style="border: none; font-weight: 900; color: #015471; font-family: 'Courier New', Courier, monospace;">123-00-9876543-2</td>
                </tr>
                <tr>
                    <td style="color: #64748b; border: none;">Atas Nama:</td>
                    <td style="border: none; font-weight: bold;">PT SWADHARMA UTAMA PRIMA</td>
                </tr>
            </table>
            <div style="color: #64748b; font-size: 8.5px; font-style: italic;">
                *Cantumkan nomor invoice <strong>{{ $income->invoice_number }}</strong> pada kolom berita transfer.
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-row" style="display: flex; justify-content: space-between;">
                <span style="float: left; color: #64748b;">Dasar Pengenaan Pajak (DPP):</span>
                <span style="float: right; font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                    Rp {{ number_format($income->gross_amount, 0, ',', '.') }}
                </span>
                <div class="clear"></div>
            </div>
            <div class="summary-row" style="display: flex; justify-content: space-between;">
                <span style="float: left; color: #64748b;">PPN Tagihan (11%):</span>
                <span style="float: right; font-weight: bold; font-family: 'Courier New', Courier, monospace; color: #015471;">
                    + Rp {{ number_format($income->ppn_amount, 0, ',', '.') }}
                </span>
                <div class="clear"></div>
            </div>
            <div class="summary-total">
                <span style="float: left;">TOTAL TAGIHAN (DPP + PPN):</span>
                <span style="float: right; font-family: 'Courier New', Courier, monospace; font-size: 13px;">
                    Rp {{ number_format($income->total_amount_with_ppn, 0, ',', '.') }}
                </span>
                <div class="clear"></div>
            </div>
            <div class="summary-row" style="background-color: #f8fafc; font-size: 9px; color: #475569;">
                <span style="float: left;">Estimasi Withholding PPh 23 (2%):</span>
                <span style="float: right; font-weight: bold; font-family: 'Courier New', Courier, monospace; color: #b45309;">
                    - Rp {{ number_format($income->pph23_amount, 0, ',', '.') }}
                </span>
                <div class="clear"></div>
            </div>
            <div class="summary-row" style="background-color: #f0fdf4; font-size: 9.5px; color: #166534;">
                <span style="float: left; font-weight: bold;">Estimasi Kas Bersih (Netto):</span>
                <span style="float: right; font-weight: 800; font-family: 'Courier New', Courier, monospace;">
                    Rp {{ number_format($income->net_profit, 0, ',', '.') }}
                </span>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Catatan Tambahan Jika Ada -->
    @if($income->notes)
        <div style="background-color: #fefce8; border: 1px solid #fef08a; padding: 6px 10px; border-radius: 6px; font-size: 9px; color: #854d0e; margin-bottom: 15px;">
            <strong>Catatan:</strong> {{ $income->notes }}
        </div>
    @endif

    <!-- Tanda Tangan Resmi -->
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="sig-role">Penerima Tagihan (Klien Mitra),</div>
                <div class="sig-name">{{ $income->vendor ? ($income->vendor->pic_name ?: $income->client_name) : $income->client_name }}</div>
                <div class="sig-title">Bagian Verifikasi & Keuangan Rekanan</div>
            </td>
            <td>
                <div class="sig-role">Hormat Kami,</div>
                <div class="sig-name">M. Syahrul Ramadhani, S.E.</div>
                <div class="sig-title">Finance & Operational Manager PT SUP</div>
            </td>
        </tr>
    </table>

</body>
</html>
