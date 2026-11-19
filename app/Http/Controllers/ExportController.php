<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\Expense;
use App\Models\Income;
use App\Models\InsurancePolicy;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    // Export Budget & Expenses Rekap to PDF (Format Memo Master or Incomes / Expenses)
    public function exportBudgetPdf(Request $request)
    {
        $tab = $request->get('tab', 'overview');
        $activeVendorId = session('active_vendor_id');
        $vendor = $activeVendorId ? Vendor::find($activeVendorId) : null;

        $budgetQuery = Budget::query();
        $expenseQuery = Expense::with(['vehicle.location', 'vendor'])->orderBy('transaction_date', 'desc');
        $incomeQuery = Income::with('vendor')->orderBy('invoice_date', 'desc');

        if ($activeVendorId) {
            $budgetQuery->where('vendor_id', $activeVendorId);
            $expenseQuery->where('vendor_id', $activeVendorId);
            $incomeQuery->where('vendor_id', $activeVendorId);
        }

        $budgets = $budgetQuery->get();
        $expenses = $expenseQuery->get();
        $incomes = $incomeQuery->get();

        $totalBudget = $budgets->sum('total_budget_36m');
        $totalExpense = $expenses->sum('amount');
        $remainingBudget = $totalBudget - $totalExpense;

        $totalIncomeGross = $incomes->sum('gross_amount');
        $totalIncomePph = round($totalIncomeGross * 0.02, 2);
        $totalIncomeNet = round($totalIncomeGross - $totalIncomePph, 2);
        $totalIncomePaid = $incomes->where('status', 'lunas')->sum('gross_amount');
        $totalIncomeUnpaid = $incomes->where('status', '!=', 'lunas')->sum('gross_amount');

        $pdf = Pdf::loadView('exports.pdf_budget_rekap', compact(
            'tab',
            'budgets',
            'expenses',
            'incomes',
            'totalBudget',
            'totalExpense',
            'remainingBudget',
            'totalIncomeGross',
            'totalIncomePph',
            'totalIncomeNet',
            'totalIncomePaid',
            'totalIncomeUnpaid',
            'vendor'
        ))->setPaper('a4', $tab === 'incomes' ? 'landscape' : 'portrait');

        $tabPrefix = match ($tab) {
            'incomes' => 'Rekap_Tagihan_Sewa_',
            'expenses' => 'Rekap_Buku_Kas_Pengeluaran_',
            default => 'Rekapitulasi_Anggaran_',
        };

        $filename = $tabPrefix.($vendor ? $vendor->code : 'MultiVendor').'_'.date('Ymd').'.pdf';

        return $pdf->download($filename);
    }

    // Export Vehicle Fleet List to PDF
    public function exportVehiclesPdf()
    {
        $activeVendorId = session('active_vendor_id');
        $vendor = $activeVendorId ? Vendor::find($activeVendorId) : null;

        $query = Vehicle::with(['location', 'vendor'])->orderBy('id', 'asc');
        if ($activeVendorId) {
            $query->where('vendor_id', $activeVendorId);
        }
        $vehicles = $query->get();

        $pdf = Pdf::loadView('exports.pdf_vehicles', compact('vehicles', 'vendor'))
            ->setPaper('a4', 'landscape');

        $filename = 'Daftar_Armada_'.($vendor ? $vendor->code : 'Semua_Vendor').'.pdf';

        return $pdf->download($filename);
    }

    // Export Specific Vehicle Service History to PDF
    public function exportVehicleHistoryPdf(Vehicle $vehicle)
    {
        $vehicle->load(['location', 'documents', 'maintenanceRecords', 'insuranceClaims']);

        $pdf = Pdf::loadView('exports.pdf_vehicle_card', compact('vehicle'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Riwayat_Unit_'.str_replace(' ', '_', $vehicle->plate_number).'.pdf');
    }

    // Export Budget & Financial Records to Excel
    public function exportFinancialExcel()
    {
        $activeVendorId = session('active_vendor_id');
        $spreadsheet = new Spreadsheet;

        // Sheet 1: Realisasi Biaya vs Plafon Anggaran
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Realisasi Anggaran');

        $sheet->setCellValue('A1', 'KOPERASI SWADHARMA');
        $sheet->setCellValue('A2', 'MONITORING ANGGARAN & BIAYA OPERASIONAL KENDARAAN');
        $sheet->setCellValue('A3', 'PROYEK SEWA MOTOR PTPN I REGIONAL 7 (36 BULAN)');

        $headers = ['No', 'Grup Kendaraan', 'Kategori Biaya', 'Pagu Anggaran 36 Bulan (Rp)', 'Realisasi Biaya (Rp)', 'Sisa Anggaran (Rp)', 'Status'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'5', $h);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $budgets = Budget::when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))->get();
        $row = 6;
        $no = 1;
        foreach ($budgets as $b) {
            $spent = Expense::where('category', $b->category)
                ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
                ->sum('amount');
            $rem = $b->total_budget_36m - $spent;

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, strtoupper(str_replace('_', ' ', $b->vehicle_group)));
            $sheet->setCellValue('C'.$row, $b->label);
            $sheet->setCellValue('D'.$row, $b->total_budget_36m);
            $sheet->setCellValue('E'.$row, $spent);
            $sheet->setCellValue('F'.$row, $rem);
            $sheet->setCellValue('G'.$row, $rem > 0 ? 'Aman' : 'Over Budget');
            $row++;
        }

        // Sheet 2: Daftar Transaksi Pengeluaran
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Buku Kas Pengeluaran');

        $headers2 = ['No', 'No Bukti', 'Tanggal', 'Plat Motor', 'Kategori', 'Uraian Biaya', 'Vendor / Bengkel', 'Nominal (Rp)', 'No Memo'];
        $col = 'A';
        foreach ($headers2 as $h) {
            $sheet2->setCellValue($col.'1', $h);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $expenses = Expense::with('vehicle')
            ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->orderBy('transaction_date', 'asc')
            ->get();
        $row = 2;
        $no = 1;
        foreach ($expenses as $e) {
            $sheet2->setCellValue('A'.$row, $no++);
            $sheet2->setCellValue('B'.$row, $e->expense_number);
            $sheet2->setCellValue('C'.$row, $e->transaction_date->format('d/m/Y'));
            $sheet2->setCellValue('D'.$row, $e->vehicle ? $e->vehicle->plate_number : '-');
            $sheet2->setCellValue('E'.$row, $e->category);
            $sheet2->setCellValue('F'.$row, $e->title);
            $sheet2->setCellValue('G'.$row, $e->recipient_vendor ?: '-');
            $sheet2->setCellValue('H'.$row, $e->amount);
            $sheet2->setCellValue('I'.$row, $e->memo_number ?: '-');
            $row++;
        }

        // Sheet 3: Daftar Tagihan Sewa Bulanan
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Pendapatan Sewa');

        $headers3 = ['No', 'No Invoice', 'Periode', 'Tanggal Invoice', 'Unit CRF', 'Unit Supra', 'Bruto (Rp)', 'PPN 11% (Rp)', 'Total Tagihan (Rp)', 'Status Pembayaran'];
        $col = 'A';
        foreach ($headers3 as $h) {
            $sheet3->setCellValue($col.'1', $h);
            $sheet3->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $incomes = Income::when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->orderBy('invoice_date', 'asc')
            ->get();
        $row = 2;
        $no = 1;
        foreach ($incomes as $inc) {
            $sheet3->setCellValue('A'.$row, $no++);
            $sheet3->setCellValue('B'.$row, $inc->invoice_number);
            $sheet3->setCellValue('C'.$row, $inc->period_month_year);
            $sheet3->setCellValue('D'.$row, $inc->invoice_date->format('d/m/Y'));
            $sheet3->setCellValue('E'.$row, $inc->crf_count);
            $sheet3->setCellValue('F'.$row, $inc->supra_count);
            $sheet3->setCellValue('G'.$row, $inc->gross_amount);
            $sheet3->setCellValue('H'.$row, $inc->ppn_amount);
            $sheet3->setCellValue('I'.$row, $inc->total_amount_with_ppn);
            $sheet3->setCellValue('J'.$row, strtoupper($inc->status));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Keuangan_Sewa_Koperasi_Swadharma_'.date('Ymd').'.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Export Vehicles to Excel
    public function exportVehiclesExcel()
    {
        $activeVendorId = session('active_vendor_id');
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Armada Motor');

        $headers = ['No', 'Plat Nomor', 'Tipe Kendaraan', 'Model', 'Lokasi Kebun', 'Pemegang / Pemakai', 'Tahun', 'Warna', 'Tarif Sewa/Bulan', 'Status'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $vehicles = Vehicle::with('location')
            ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->orderBy('id', 'asc')
            ->get();
        $row = 2;
        $no = 1;
        foreach ($vehicles as $v) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $v->plate_number);
            $sheet->setCellValue('C'.$row, $v->vehicle_type_label);
            $sheet->setCellValue('D'.$row, $v->model_name);
            $sheet->setCellValue('E'.$row, $v->location ? $v->location->name : '-');
            $sheet->setCellValue('F'.$row, $v->user_holder ?: '-');
            $sheet->setCellValue('G'.$row, $v->year);
            $sheet->setCellValue('H'.$row, $v->color);
            $sheet->setCellValue('I'.$row, $v->rental_price_per_month);
            $sheet->setCellValue('J'.$row, $v->status_badge['label']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data_Armada_76_Motor_'.date('Ymd').'.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Export Single Client Request to PDF
    public function exportClientRequestPdf(ClientRequest $clientRequest)
    {
        $clientRequest->load(['vehicle.location', 'vendor', 'user']);

        // Extract metadata blocks and complaints from description (handling both \r\n and \n)
        $normalizedDesc = str_replace(["\r\n", "\r"], "\n", $clientRequest->description ?? '');
        $descLines = explode("\n", $normalizedDesc);

        $structuredData = [
            'pic_name' => '',
            'pic_phone' => '',
            'operational_status' => '',
            'unit_position' => '',
            'physical_notes' => '',
            'workshop_phone' => '',
            'mechanic_name' => '',
            'mechanic_phone' => '',
        ];

        $complaintBlocks = [];
        $cleanDescLines = [];
        $currentComplaint = null;

        foreach ($descLines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            if (str_starts_with($trimmed, '[PIC Pengaju:')) {
                $content = trim($trimmed, '[]');
                if (preg_match('/PIC Pengaju:\s*([^-]+?)(?:\s*-\s*Telp:\s*(.*))?$/i', $content, $pm)) {
                    $structuredData['pic_name'] = trim($pm[1] ?? '');
                    $structuredData['pic_phone'] = trim($pm[2] ?? '');
                }
            } elseif (str_starts_with($trimmed, '[Kondisi Operasional:') || str_contains($trimmed, 'Kondisi Operasional:')) {
                $content = trim($trimmed, '[]');
                $parts = explode('|', $content);
                foreach ($parts as $part) {
                    $partTrim = trim($part);
                    if (str_starts_with($partTrim, 'Kondisi Operasional:')) {
                        $structuredData['operational_status'] = trim(substr($partTrim, 20));
                    } elseif (str_starts_with($partTrim, 'Posisi Unit:')) {
                        $structuredData['unit_position'] = trim(substr($partTrim, 12));
                    } elseif (str_starts_with($partTrim, 'Catatan Fisik:')) {
                        $structuredData['physical_notes'] = trim(substr($partTrim, 14));
                    }
                }
            } elseif (str_starts_with($trimmed, '[Telp Bengkel:') || str_contains($trimmed, 'Telp Bengkel:') || str_contains($trimmed, 'Mekanik:')) {
                $content = trim($trimmed, '[]');
                $parts = explode('|', $content);
                foreach ($parts as $part) {
                    $partTrim = trim($part);
                    if (str_starts_with($partTrim, 'Telp Bengkel:')) {
                        $structuredData['workshop_phone'] = trim(substr($partTrim, 13));
                    } elseif (str_starts_with($partTrim, 'Mekanik:')) {
                        $mContent = trim(substr($partTrim, 8));
                        if (preg_match('/^(.*?)\s*\(Telp:\s*(.*?)\)$/i', $mContent, $mm)) {
                            $structuredData['mechanic_name'] = trim($mm[1]);
                            $structuredData['mechanic_phone'] = trim($mm[2]);
                        } else {
                            $structuredData['mechanic_name'] = $mContent;
                        }
                    }
                }
            } elseif (preg_match('/^\[Keluhan #(\d+): (.*?)\]$/', $trimmed, $m)) {
                if ($currentComplaint) {
                    $complaintBlocks[] = $currentComplaint;
                }
                $currentComplaint = [
                    'num' => $m[1],
                    'category' => $m[2],
                    'title' => '',
                    'cost' => '',
                    'desc' => '',
                ];
            } elseif ($currentComplaint && str_starts_with($trimmed, 'Judul: ')) {
                $titleLine = substr($trimmed, 7);
                if (preg_match('/^(.*?) \(Estimasi: (.*?)\)$/', $titleLine, $cm)) {
                    $currentComplaint['title'] = $cm[1];
                    $currentComplaint['cost'] = $cm[2];
                } else {
                    $currentComplaint['title'] = $titleLine;
                }
            } elseif ($currentComplaint && str_starts_with($trimmed, 'Uraian: ')) {
                $currentComplaint['desc'] = substr($trimmed, 8);
            } elseif ($currentComplaint) {
                $currentComplaint['desc'] .= (! empty($currentComplaint['desc']) ? "\n" : '').$trimmed;
            } elseif (! str_starts_with($trimmed, '[Kategori:')) {
                $cleanDescLines[] = $trimmed;
            }
        }

        if ($currentComplaint) {
            $complaintBlocks[] = $currentComplaint;
        }

        if (empty($complaintBlocks) && $clientRequest->request_type === 'perbaikan_servis') {
            $complaintBlocks[] = [
                'num' => '1',
                'category' => 'Pengajuan Perbaikan / Servis',
                'title' => $clientRequest->title,
                'cost' => $clientRequest->estimated_cost > 0 ? 'Rp '.number_format($clientRequest->estimated_cost, 0, ',', '.') : '',
                'desc' => implode("\n", $cleanDescLines) ?: $clientRequest->description,
            ];
        }

        $remainingDescription = implode("\n", $cleanDescLines);

        // Separate vehicle angle photos
        $proofFiles = $clientRequest->proof_files ?? [];
        $vehicleAnglePhotos = [];
        $otherDocs = [];

        foreach ($proofFiles as $pf) {
            $name = $pf['name'] ?? '';
            $path = $pf['path'] ?? '';
            if (str_contains($name, 'Tampak Depan')) {
                $vehicleAnglePhotos['front'] = $pf;
            } elseif (str_contains($name, 'Samping Kanan')) {
                $vehicleAnglePhotos['right'] = $pf;
            } elseif (str_contains($name, 'Samping Kiri')) {
                $vehicleAnglePhotos['left'] = $pf;
            } elseif (str_contains($name, 'Tampak Belakang')) {
                $vehicleAnglePhotos['behind'] = $pf;
            } else {
                $otherDocs[] = $pf;
            }
        }

        $pdf = Pdf::loadView('exports.pdf_client_request', compact(
            'clientRequest',
            'structuredData',
            'complaintBlocks',
            'remainingDescription',
            'vehicleAnglePhotos',
            'otherDocs'
        ))->setPaper('a4', 'portrait');

        $filename = 'Tiket_Permohonan_REQ-'.str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT).'_'.($clientRequest->vehicle ? str_replace(' ', '_', $clientRequest->vehicle->plate_number) : 'Unit').'.pdf';

        return $pdf->download($filename);
    }

    // Export Single Client Request to Word Document (.doc)
    public function exportClientRequestDoc(ClientRequest $clientRequest)
    {
        $clientRequest->load(['vehicle.location', 'vendor', 'user']);

        // Extract metadata blocks and complaints from description (handling both \r\n and \n)
        $normalizedDesc = str_replace(["\r\n", "\r"], "\n", $clientRequest->description ?? '');
        $descLines = explode("\n", $normalizedDesc);

        $structuredData = [
            'pic_name' => '',
            'pic_phone' => '',
            'operational_status' => '',
            'unit_position' => '',
            'physical_notes' => '',
            'workshop_phone' => '',
            'mechanic_name' => '',
            'mechanic_phone' => '',
        ];

        $complaintBlocks = [];
        $cleanDescLines = [];
        $currentComplaint = null;

        foreach ($descLines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            if (str_starts_with($trimmed, '[PIC Pengaju:')) {
                $content = trim($trimmed, '[]');
                if (preg_match('/PIC Pengaju:\s*([^-]+?)(?:\s*-\s*Telp:\s*(.*))?$/i', $content, $pm)) {
                    $structuredData['pic_name'] = trim($pm[1] ?? '');
                    $structuredData['pic_phone'] = trim($pm[2] ?? '');
                }
            } elseif (str_starts_with($trimmed, '[Kondisi Operasional:') || str_contains($trimmed, 'Kondisi Operasional:')) {
                $content = trim($trimmed, '[]');
                $parts = explode('|', $content);
                foreach ($parts as $part) {
                    $partTrim = trim($part);
                    if (str_starts_with($partTrim, 'Kondisi Operasional:')) {
                        $structuredData['operational_status'] = trim(substr($partTrim, 20));
                    } elseif (str_starts_with($partTrim, 'Posisi Unit:')) {
                        $structuredData['unit_position'] = trim(substr($partTrim, 12));
                    } elseif (str_starts_with($partTrim, 'Catatan Fisik:')) {
                        $structuredData['physical_notes'] = trim(substr($partTrim, 14));
                    }
                }
            } elseif (str_starts_with($trimmed, '[Telp Bengkel:') || str_contains($trimmed, 'Telp Bengkel:') || str_contains($trimmed, 'Mekanik:')) {
                $content = trim($trimmed, '[]');
                $parts = explode('|', $content);
                foreach ($parts as $part) {
                    $partTrim = trim($part);
                    if (str_starts_with($partTrim, 'Telp Bengkel:')) {
                        $structuredData['workshop_phone'] = trim(substr($partTrim, 13));
                    } elseif (str_starts_with($partTrim, 'Mekanik:')) {
                        $mContent = trim(substr($partTrim, 8));
                        if (preg_match('/^(.*?)\s*\(Telp:\s*(.*?)\)$/i', $mContent, $mm)) {
                            $structuredData['mechanic_name'] = trim($mm[1]);
                            $structuredData['mechanic_phone'] = trim($mm[2]);
                        } else {
                            $structuredData['mechanic_name'] = $mContent;
                        }
                    }
                }
            } elseif (preg_match('/^\[Keluhan #(\d+): (.*?)\]$/', $trimmed, $m)) {
                if ($currentComplaint) {
                    $complaintBlocks[] = $currentComplaint;
                }
                $currentComplaint = [
                    'num' => $m[1],
                    'category' => $m[2],
                    'title' => '',
                    'cost' => '',
                    'desc' => '',
                ];
            } elseif ($currentComplaint && str_starts_with($trimmed, 'Judul: ')) {
                $titleLine = substr($trimmed, 7);
                if (preg_match('/^(.*?) \(Estimasi: (.*?)\)$/', $titleLine, $cm)) {
                    $currentComplaint['title'] = $cm[1];
                    $currentComplaint['cost'] = $cm[2];
                } else {
                    $currentComplaint['title'] = $titleLine;
                }
            } elseif ($currentComplaint && str_starts_with($trimmed, 'Uraian: ')) {
                $currentComplaint['desc'] = substr($trimmed, 8);
            } elseif ($currentComplaint) {
                $currentComplaint['desc'] .= (! empty($currentComplaint['desc']) ? "\n" : '').$trimmed;
            } elseif (! str_starts_with($trimmed, '[Kategori:')) {
                $cleanDescLines[] = $trimmed;
            }
        }

        if ($currentComplaint) {
            $complaintBlocks[] = $currentComplaint;
        }

        if (empty($complaintBlocks) && $clientRequest->request_type === 'perbaikan_servis') {
            $complaintBlocks[] = [
                'num' => '1',
                'category' => 'Pengajuan Perbaikan / Servis',
                'title' => $clientRequest->title,
                'cost' => $clientRequest->estimated_cost > 0 ? 'Rp '.number_format($clientRequest->estimated_cost, 0, ',', '.') : '',
                'desc' => implode("\n", $cleanDescLines) ?: $clientRequest->description,
            ];
        }

        $remainingDescription = implode("\n", $cleanDescLines);

        $proofFiles = $clientRequest->proof_files ?? [];
        $vehicleAnglePhotos = [];
        $otherDocs = [];

        foreach ($proofFiles as $pf) {
            $name = $pf['name'] ?? '';
            if (str_contains($name, 'Tampak Depan')) {
                $vehicleAnglePhotos['front'] = $pf;
            } elseif (str_contains($name, 'Samping Kanan')) {
                $vehicleAnglePhotos['right'] = $pf;
            } elseif (str_contains($name, 'Samping Kiri')) {
                $vehicleAnglePhotos['left'] = $pf;
            } elseif (str_contains($name, 'Tampak Belakang')) {
                $vehicleAnglePhotos['behind'] = $pf;
            } else {
                $otherDocs[] = $pf;
            }
        }

        $content = view('exports.pdf_client_request', compact(
            'clientRequest',
            'structuredData',
            'complaintBlocks',
            'remainingDescription',
            'vehicleAnglePhotos',
            'otherDocs'
        ))->render();

        $filename = 'Tiket_Permohonan_REQ-'.str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT).'_'.($clientRequest->vehicle ? str_replace(' ', '_', $clientRequest->vehicle->plate_number) : 'Unit').'.doc';

        return response($content, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Export Single Income Invoice to PDF (View Inline or Download)
    public function exportSingleInvoicePdf(Request $request, Income $income)
    {
        $action = $request->get('action', 'stream'); // 'stream' (preview) or 'download'

        $pdf = Pdf::loadView('exports.pdf_invoice_single', compact('income'))
            ->setPaper('a4', 'portrait');

        $cleanNumber = str_replace(['/', '\\', ' '], '_', $income->invoice_number);
        $fileName = 'Faktur_'.$cleanNumber.'.pdf';

        if ($action === 'download') {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    public function exportClientsExcel(): StreamedResponse
    {
        [$headers, $rows] = $this->buildClientsExportData();

        return $this->streamExcel('Master_Klien_Kopswa_'.date('Ymd').'.xlsx', [
            ['title' => 'Master Data Klien', 'headers' => $headers, 'rows' => $rows],
        ]);
    }

    public function exportClientsPdf()
    {
        [$headers, $rows, $summary] = $this->buildClientsExportData();

        return $this->streamPdfRekap('Rekap Master Data Klien', 'Data Perusahaan Mitra & NPWP Coretax', $headers, $rows, $summary, 'Rekap_Master_Klien_'.date('Ymd').'.pdf');
    }

    public function exportVendorsExcel(): StreamedResponse
    {
        [$headers, $rows] = $this->buildVendorsExportData();

        return $this->streamExcel('Data_Kontrak_Sewa_Kopswa_'.date('Ymd').'.xlsx', [
            ['title' => 'Kontrak Sewa', 'headers' => $headers, 'rows' => $rows],
        ]);
    }

    public function exportVendorsPdf()
    {
        [$headers, $rows, $summary] = $this->buildVendorsExportData();

        return $this->streamPdfRekap('Rekap Data Kontrak Sewa', 'Daftar Kontrak, Nilai Sewa, Siklus & Metode Pembayaran', $headers, $rows, $summary, 'Rekap_Kontrak_Sewa_'.date('Ymd').'.pdf');
    }

    public function exportMaintenanceExcel(): StreamedResponse
    {
        [$headers, $rows] = $this->buildMaintenanceExportData();

        return $this->streamExcel('Riwayat_Pemeliharaan_Kopswa_'.date('Ymd').'.xlsx', [
            ['title' => 'Pemeliharaan & Servis', 'headers' => $headers, 'rows' => $rows],
        ]);
    }

    public function exportMaintenancePdf()
    {
        [$headers, $rows, $summary] = $this->buildMaintenanceExportData();

        return $this->streamPdfRekap('Rekap Pemeliharaan & Servis Unit', $this->activeVendorSubtitle(), $headers, $rows, $summary, 'Rekap_Pemeliharaan_'.date('Ymd').'.pdf');
    }

    public function exportDocumentsExcel(): StreamedResponse
    {
        [$headers, $rows] = $this->buildDocumentsExportData();

        return $this->streamExcel('Dokumen_Pajak_STNK_Kopswa_'.date('Ymd').'.xlsx', [
            ['title' => 'Dokumen & Pajak', 'headers' => $headers, 'rows' => $rows],
        ]);
    }

    public function exportDocumentsPdf()
    {
        [$headers, $rows, $summary] = $this->buildDocumentsExportData();

        return $this->streamPdfRekap('Rekap Dokumen & Pajak Kendaraan', $this->activeVendorSubtitle(), $headers, $rows, $summary, 'Rekap_Dokumen_Pajak_'.date('Ymd').'.pdf');
    }

    public function exportInsuranceExcel(): StreamedResponse
    {
        [$policyHeaders, $policyRows, $summary, $claimHeaders, $claimRows] = $this->buildInsuranceExportData();

        return $this->streamExcel('Asuransi_Klaim_Kopswa_'.date('Ymd').'.xlsx', [
            ['title' => 'Polis Asuransi', 'headers' => $policyHeaders, 'rows' => $policyRows],
            ['title' => 'Klaim & Insiden', 'headers' => $claimHeaders, 'rows' => $claimRows],
        ]);
    }

    public function exportInsurancePdf(Request $request)
    {
        [$policyHeaders, $policyRows, $summary, $claimHeaders, $claimRows] = $this->buildInsuranceExportData();
        $tab = $request->get('tab', 'policies');

        if ($tab === 'claims') {
            return $this->streamPdfRekap('Rekap Klaim & Insiden Asuransi', $this->activeVendorSubtitle(), $claimHeaders, $claimRows, $summary, 'Rekap_Klaim_Asuransi_'.date('Ymd').'.pdf');
        }

        return $this->streamPdfRekap('Rekap Data Polis Asuransi Unit', $this->activeVendorSubtitle(), $policyHeaders, $policyRows, $summary, 'Rekap_Polis_Asuransi_'.date('Ymd').'.pdf');
    }

    // Export 2-Sheet Excel matching qryPersewaan.xlsx (Data Kontrak + Data Kdr Kontrak)
    public function exportPersewaanExcel(): StreamedResponse
    {
        $activeVendorId = session('active_vendor_id');

        $vendors = Vendor::with('client')
            ->withCount('vehicles')
            ->when($activeVendorId, fn ($q) => $q->where('id', $activeVendorId))
            ->orderBy('id')
            ->get();

        $vehicles = Vehicle::with(['vendor.client'])
            ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->orderBy('vendor_id')
            ->orderBy('id')
            ->get();

        $kontrakHeaders = [
            'NoKontrak', 'TglKontrak', 'PrdAwal', 'PrdAkhir', 'JangkaWaktu', 'CaraBayar',
            'NilaiKontrakTotal', 'NilaiKontrakUtama', 'NilaiBulananUtama',
            'NilaiKontrakTambahan', 'NilaiBulananTambahan', 'Penyewa', 'Alamat', 'TelpPenyewa', 'JumlahKdr',
        ];

        $kontrakRows = $vendors->map(function ($v) {
            $caraBayar = match ($v->billing_cycle) {
                'triwulanan' => '3 Bulan',
                'semesteran' => '6 Bulan',
                'tahunan' => '12 Bulan',
                '2_tahunan' => '24 Bulan',
                default => '1 Bulan',
            };

            return [
                $v->contract_number ?: $v->code,
                $v->contract_date?->format('d/m/Y') ?: '',
                $v->contract_start_date?->format('d/m/Y') ?: '',
                $v->contract_end_date?->format('d/m/Y') ?: '',
                $v->duration_months ?: '',
                $caraBayar,
                (float) $v->contract_value,
                (float) $v->main_contract_value,
                (float) $v->main_monthly_value,
                (float) $v->additional_contract_value,
                (float) $v->additional_monthly_value,
                $v->client?->name ?: $v->name,
                $v->client?->address ?: '-',
                $v->phone ?: ($v->client?->phone ?: '-'),
                $v->vehicles_count,
            ];
        })->all();

        $kdrHeaders = [
            'NoKontrak', 'Pengguna', 'Jenis', 'Merk', 'Tipe', 'Item', 'Nopol',
            'NilaiKontrak', 'NilaiKontrakBulanan', 'NilaiTambahan', 'NilaiTambahanBulanan',
            'NoMesin', 'NoRangka', 'HargaBeli', 'Perolehan', 'PPnPM', 'Residu', 'PrdSusut',
        ];

        $kdrRows = $vehicles->map(function ($veh) {
            return [
                $veh->vendor?->contract_number ?: '-',
                $veh->user_holder ?: ($veh->vendor?->client?->name ?: ($veh->vendor?->name ?: '-')),
                ucfirst($veh->asset_type ?: 'Motor'),
                $veh->brand ?: '-',
                $veh->model_name ?: '-',
                $veh->type_name ?: '-',
                $veh->plate_number ?: '-',
                (float) $veh->total_contract_value,
                (float) $veh->rental_price_per_month,
                (float) $veh->additional_contract_value,
                (float) $veh->additional_monthly_value,
                $veh->engine_number ?: '-',
                $veh->frame_number ?: '-',
                (float) $veh->invoice_value,
                (float) $veh->acquisition_value,
                (float) $veh->ppn_pm,
                (float) $veh->residual_percentage,
                $veh->useful_life_months ?: 48,
            ];
        })->all();

        return $this->streamExcel('qryPersewaan_'.date('Ymd').'.xlsx', [
            ['title' => 'Data Kontrak', 'headers' => $kontrakHeaders, 'rows' => $kontrakRows],
            ['title' => 'Data Kdr Kontrak', 'headers' => $kdrHeaders, 'rows' => $kdrRows],
        ]);
    }

    private function buildClientsExportData(): array
    {
        $clients = Client::withCount('contracts')->orderBy('name')->get();
        $headers = ['Kode Klien', 'Nama Perusahaan', 'NPWP', 'NITKU', 'PIC', 'No. Telepon', 'Email', 'Alamat', 'Jml Kontrak'];
        $rows = $clients->map(fn ($c) => [
            $c->code ?: '-',
            $c->name,
            $c->npwp ?: '-',
            $c->nitku ?: '-',
            $c->pic_name ?: '-',
            $c->phone ?: '-',
            $c->email ?: '-',
            $c->address ?: '-',
            $c->contracts_count,
        ])->all();

        $summary = 'Total Klien Terdaftar: <strong>'.$clients->count().'</strong> Perusahaan';

        return [$headers, $rows, $summary];
    }

    private function buildVendorsExportData(): array
    {
        $vendors = Vendor::with('client')->withCount('vehicles')->orderBy('id')->get();
        $headers = ['Kode', 'No. Kontrak', 'Klien', 'Tgl Kontrak', 'Periode Awal', 'Periode Akhir', 'Tenor (Bln)', 'Cara Bayar', 'Pembayaran', 'Nilai Utama', 'Bulanan Utama', 'Nilai Tambahan', 'Nilai Total', 'Unit', 'Status'];
        $rows = $vendors->map(fn ($v) => [
            $v->code,
            $v->contract_number ?: '-',
            $v->client?->name ?: $v->name,
            $v->contract_date?->format('d/m/Y') ?: '-',
            $v->contract_start_date?->format('d/m/Y') ?: '-',
            $v->contract_end_date?->format('d/m/Y') ?: '-',
            $v->duration_months ?: '-',
            $v->billing_cycle_label,
            $v->payment_method_label,
            number_format((float) $v->main_contract_value, 0, ',', '.'),
            number_format((float) $v->main_monthly_value, 0, ',', '.'),
            number_format((float) $v->additional_contract_value, 0, ',', '.'),
            number_format((float) $v->contract_value, 0, ',', '.'),
            $v->vehicles_count,
            strtoupper($v->status),
        ])->all();

        $totalNilai = $vendors->sum('contract_value');
        $summary = 'Total Kontrak: <strong>'.$vendors->count().'</strong> &bull; Aktif: <strong>'.$vendors->where('status', 'active')->count().'</strong> &bull; Total Nilai Kontrak: <strong>Rp '.number_format((float) $totalNilai, 0, ',', '.').'</strong>';

        return [$headers, $rows, $summary];
    }

    private function buildMaintenanceExportData(): array
    {
        $activeVendorId = session('active_vendor_id');
        $records = MaintenanceRecord::with(['vehicle.vendor', 'vehicle.location'])
            ->when($activeVendorId, fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('vendor_id', $activeVendorId)))
            ->orderByDesc('service_date')
            ->get();

        $headers = ['Tgl Servis', 'Plat / Kode', 'Model Aset', 'Kontrak', 'Bengkel / Vendor', 'Odometer (KM)', 'Jenis Pekerjaan', 'Rincian / Catatan', 'Biaya (Rp)', 'Status'];
        $rows = $records->map(fn ($m) => [
            $m->service_date?->format('d/m/Y') ?: '-',
            $m->vehicle?->plate_number ?: '-',
            $m->vehicle?->model_name ?: '-',
            $m->vehicle?->vendor?->contract_number ?: ($m->vehicle?->vendor?->name ?: '-'),
            $m->workshop_name ?: '-',
            $m->odometer_km ? number_format($m->odometer_km, 0, ',', '.') : '-',
            $m->service_type ?: '-',
            $m->description ?: ($m->notes ?: '-'),
            number_format((float) $m->cost, 0, ',', '.'),
            strtoupper($m->status ?: '-'),
        ])->all();

        $summary = 'Total Catatan Servis: <strong>'.$records->count().'</strong> &bull; Total Biaya: <strong>Rp '.number_format((float) $records->sum('cost'), 0, ',', '.').'</strong>';

        return [$headers, $rows, $summary];
    }

    private function buildDocumentsExportData(): array
    {
        $activeVendorId = session('active_vendor_id');
        $docs = VehicleDocument::with(['vehicle.vendor'])
            ->when($activeVendorId, fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('vendor_id', $activeVendorId)))
            ->orderBy('expiry_date')
            ->get();

        $headers = ['Plat / Kode', 'Model Aset', 'Kontrak', 'Jenis Dokumen', 'Nomor Dokumen', 'Jatuh Tempo', 'Status', 'Catatan'];
        $rows = $docs->map(fn ($d) => [
            $d->vehicle?->plate_number ?: '-',
            $d->vehicle?->model_name ?: '-',
            $d->vehicle?->vendor?->contract_number ?: ($d->vehicle?->vendor?->name ?: '-'),
            strtoupper(str_replace('_', ' ', $d->document_type)),
            $d->document_number ?: '-',
            $d->expiry_date?->format('d/m/Y') ?: '-',
            strtoupper($d->status ?: '-'),
            $d->notes ?: '-',
        ])->all();

        $summary = 'Total Dokumen Tercatat: <strong>'.$docs->count().'</strong>';

        return [$headers, $rows, $summary];
    }

    private function buildInsuranceExportData(): array
    {
        $activeVendorId = session('active_vendor_id');
        $policies = InsurancePolicy::with(['vehicle', 'vendor'])
            ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->orderBy('end_date')
            ->get();

        $claims = ClientRequest::with(['vehicle', 'vendor'])
            ->whereIn('request_type', ['kehilangan', 'kecelakaan', 'asuransi', 'klaim_asuransi'])
            ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->latest()
            ->get();

        $policyHeaders = ['No. Polis', 'Perusahaan Asuransi', 'Plat / Kode Unit', 'Kontrak', 'Pertanggungan', 'Mulai', 'Berakhir', 'Premi (Rp)', 'Nilai Pertanggungan (Rp)', 'Status'];
        $policyRows = $policies->map(fn ($p) => [
            $p->policy_number,
            $p->insurance_company,
            $p->vehicle?->plate_number ?: '-',
            $p->vendor?->contract_number ?: ($p->vendor?->name ?: '-'),
            strtoupper(str_replace('_', ' ', $p->coverage_type)),
            $p->start_date?->format('d/m/Y') ?: '-',
            $p->end_date?->format('d/m/Y') ?: '-',
            number_format((float) $p->premium_amount, 0, ',', '.'),
            number_format((float) $p->coverage_amount, 0, ',', '.'),
            strtoupper($p->status ?: '-'),
        ])->all();

        $claimHeaders = ['Tgl Laporan', 'Plat / Kode Unit', 'Kontrak', 'Jenis Kasus', 'Status', 'Deskripsi'];
        $claimRows = $claims->map(fn ($c) => [
            $c->created_at?->format('d/m/Y') ?: '-',
            $c->vehicle?->plate_number ?: '-',
            $c->vendor?->contract_number ?: ($c->vendor?->name ?: '-'),
            strtoupper(str_replace('_', ' ', $c->request_type)),
            strtoupper($c->status ?: '-'),
            $c->description ?: '-',
        ])->all();

        $summary = 'Total Polis: <strong>'.$policies->count().'</strong> &bull; Total Premi: <strong>Rp '.number_format((float) $policies->sum('premium_amount'), 0, ',', '.').'</strong> &bull; Total Klaim: <strong>'.$claims->count().'</strong>';

        return [$policyHeaders, $policyRows, $summary, $claimHeaders, $claimRows];
    }

    private function activeVendorSubtitle(): string
    {
        $activeVendorId = session('active_vendor_id');
        if (! $activeVendorId) {
            return 'Seluruh Kontrak Aktif';
        }
        $v = Vendor::find($activeVendorId);

        return $v ? 'Kontrak: '.($v->contract_number ?: $v->name) : 'Seluruh Kontrak Aktif';
    }

    private function streamPdfRekap(string $title, string $subtitle, array $headers, array $rows, string $summaryText, string $filename)
    {
        $pdf = Pdf::loadView('exports.pdf_table_rekap', compact('title', 'subtitle', 'headers', 'rows', 'summaryText'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function streamExcel(string $filename, array $sheets): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;

        foreach ($sheets as $index => $sheetData) {
            $sheet = $index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle(substr($sheetData['title'], 0, 31));

            $headers = $sheetData['headers'];
            $rows = $sheetData['rows'];

            foreach ($headers as $colIdx => $header) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue($colLetter.'1', $header);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }

            $lastCol = Coordinate::stringFromColumnIndex(max(1, count($headers)));
            $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF015471');
            $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            foreach ($rows as $rowIdx => $row) {
                $excelRow = $rowIdx + 2;
                foreach ($row as $colIdx => $val) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
                    $sheet->setCellValue($colLetter.$excelRow, $val);
                }
            }
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
