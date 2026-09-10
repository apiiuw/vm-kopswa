<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Budget;
use App\Models\MaintenanceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    // Export Budget & Expenses Rekap to PDF (Format Memo Master)
    public function exportBudgetPdf()
    {
        $activeVendorId = session('active_vendor_id');
        $vendor = $activeVendorId ? \App\Models\Vendor::find($activeVendorId) : null;

        $budgetQuery = Budget::query();
        $expenseQuery = Expense::with('vehicle')->orderBy('transaction_date', 'asc');

        if ($activeVendorId) {
            $budgetQuery->where('vendor_id', $activeVendorId);
            $expenseQuery->where('vendor_id', $activeVendorId);
        }

        $budgets = $budgetQuery->get();
        $expenses = $expenseQuery->get();
        $totalBudget = $budgets->sum('total_budget_36m');
        $totalExpense = $expenses->sum('amount');
        $remainingBudget = $totalBudget - $totalExpense;

        $pdf = Pdf::loadView('exports.pdf_budget_rekap', compact(
            'budgets',
            'expenses',
            'totalBudget',
            'totalExpense',
            'remainingBudget',
            'vendor'
        ))->setPaper('a4', 'portrait');

        $filename = 'Rekapitulasi_Anggaran_' . ($vendor ? $vendor->code : 'MultiVendor') . '.pdf';
        return $pdf->download($filename);
    }

    // Export Vehicle Fleet List to PDF
    public function exportVehiclesPdf()
    {
        $activeVendorId = session('active_vendor_id');
        $vendor = $activeVendorId ? \App\Models\Vendor::find($activeVendorId) : null;

        $query = Vehicle::with(['location', 'vendor'])->orderBy('id', 'asc');
        if ($activeVendorId) {
            $query->where('vendor_id', $activeVendorId);
        }
        $vehicles = $query->get();
        
        $pdf = Pdf::loadView('exports.pdf_vehicles', compact('vehicles', 'vendor'))
            ->setPaper('a4', 'landscape');

        $filename = 'Daftar_Armada_' . ($vendor ? $vendor->code : 'Semua_Vendor') . '.pdf';
        return $pdf->download($filename);
    }

    // Export Specific Vehicle Service History to PDF
    public function exportVehicleHistoryPdf(Vehicle $vehicle)
    {
        $vehicle->load(['location', 'documents', 'maintenanceRecords', 'insuranceClaims']);
        
        $pdf = Pdf::loadView('exports.pdf_vehicle_card', compact('vehicle'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Riwayat_Unit_' . str_replace(' ', '_', $vehicle->plate_number) . '.pdf');
    }

    // Export Budget & Financial Records to Excel
    public function exportFinancialExcel()
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Realisasi Biaya vs Plafon Anggaran
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Realisasi Anggaran');

        $sheet->setCellValue('A1', 'PT SWADHARMA UTAMA PRIMA');
        $sheet->setCellValue('A2', 'MONITORING ANGGARAN & BIAYA OPERASIONAL KENDARAAN');
        $sheet->setCellValue('A3', 'PROYEK SEWA MOTOR PTPN I REGIONAL 7 (36 BULAN)');

        $headers = ['No', 'Grup Kendaraan', 'Kategori Biaya', 'Pagu Anggaran 36 Bulan (Rp)', 'Realisasi Biaya (Rp)', 'Sisa Anggaran (Rp)', 'Status'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '5', $h);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $budgets = Budget::all();
        $row = 6;
        $no = 1;
        foreach ($budgets as $b) {
            $spent = Expense::where('category', $b->category)->sum('amount');
            $rem = $b->total_budget_36m - $spent;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, strtoupper(str_replace('_', ' ', $b->vehicle_group)));
            $sheet->setCellValue('C' . $row, $b->label);
            $sheet->setCellValue('D' . $row, $b->total_budget_36m);
            $sheet->setCellValue('E' . $row, $spent);
            $sheet->setCellValue('F' . $row, $rem);
            $sheet->setCellValue('G' . $row, $rem > 0 ? 'Aman' : 'Over Budget');
            $row++;
        }

        // Sheet 2: Daftar Transaksi Pengeluaran
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Buku Kas Pengeluaran');

        $headers2 = ['No', 'No Bukti', 'Tanggal', 'Plat Motor', 'Kategori', 'Uraian Biaya', 'Vendor / Bengkel', 'Nominal (Rp)', 'No Memo'];
        $col = 'A';
        foreach ($headers2 as $h) {
            $sheet2->setCellValue($col . '1', $h);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $expenses = Expense::with('vehicle')->orderBy('transaction_date', 'asc')->get();
        $row = 2;
        $no = 1;
        foreach ($expenses as $e) {
            $sheet2->setCellValue('A' . $row, $no++);
            $sheet2->setCellValue('B' . $row, $e->expense_number);
            $sheet2->setCellValue('C' . $row, $e->transaction_date->format('d/m/Y'));
            $sheet2->setCellValue('D' . $row, $e->vehicle ? $e->vehicle->plate_number : '-');
            $sheet2->setCellValue('E' . $row, $e->category);
            $sheet2->setCellValue('F' . $row, $e->title);
            $sheet2->setCellValue('G' . $row, $e->recipient_vendor ?: '-');
            $sheet2->setCellValue('H' . $row, $e->amount);
            $sheet2->setCellValue('I' . $row, $e->memo_number ?: '-');
            $row++;
        }

        // Sheet 3: Daftar Tagihan Sewa Bulanan
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Pendapatan Sewa');

        $headers3 = ['No', 'No Invoice', 'Periode', 'Tanggal Invoice', 'Unit CRF', 'Unit Supra', 'Bruto (Rp)', 'PPN 11% (Rp)', 'Total Tagihan (Rp)', 'Status Pembayaran'];
        $col = 'A';
        foreach ($headers3 as $h) {
            $sheet3->setCellValue($col . '1', $h);
            $sheet3->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $incomes = Income::orderBy('invoice_date', 'asc')->get();
        $row = 2;
        $no = 1;
        foreach ($incomes as $inc) {
            $sheet3->setCellValue('A' . $row, $no++);
            $sheet3->setCellValue('B' . $row, $inc->invoice_number);
            $sheet3->setCellValue('C' . $row, $inc->period_month_year);
            $sheet3->setCellValue('D' . $row, $inc->invoice_date->format('d/m/Y'));
            $sheet3->setCellValue('E' . $row, $inc->crf_count);
            $sheet3->setCellValue('F' . $row, $inc->supra_count);
            $sheet3->setCellValue('G' . $row, $inc->gross_amount);
            $sheet3->setCellValue('H' . $row, $inc->ppn_amount);
            $sheet3->setCellValue('I' . $row, $inc->total_amount_with_ppn);
            $sheet3->setCellValue('J' . $row, strtoupper($inc->status));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Keuangan_Sewa_PT_SUP_' . date('Ymd') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Export Vehicles to Excel
    public function exportVehiclesExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Armada Motor');

        $headers = ['No', 'Plat Nomor', 'Tipe Kendaraan', 'Model', 'Lokasi Kebun', 'Pemegang / Pemakai', 'Tahun', 'Warna', 'Tarif Sewa/Bulan', 'Status'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $vehicles = Vehicle::with('location')->orderBy('id', 'asc')->get();
        $row = 2;
        $no = 1;
        foreach ($vehicles as $v) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $v->plate_number);
            $sheet->setCellValue('C' . $row, $v->vehicle_type_label);
            $sheet->setCellValue('D' . $row, $v->model_name);
            $sheet->setCellValue('E' . $row, $v->location ? $v->location->name : '-');
            $sheet->setCellValue('F' . $row, $v->user_holder ?: '-');
            $sheet->setCellValue('G' . $row, $v->year);
            $sheet->setCellValue('H' . $row, $v->color);
            $sheet->setCellValue('I' . $row, $v->rental_price_per_month);
            $sheet->setCellValue('J' . $row, $v->status_badge['label']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data_Armada_76_Motor_' . date('Ymd') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Export Single Client Request to PDF
    public function exportClientRequestPdf(\App\Models\ClientRequest $clientRequest)
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
            if (empty($trimmed)) continue;

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
                $currentComplaint['desc'] .= (!empty($currentComplaint['desc']) ? "\n" : '') . $trimmed;
            } elseif (!str_starts_with($trimmed, '[Kategori:')) {
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
                'cost' => $clientRequest->estimated_cost > 0 ? 'Rp ' . number_format($clientRequest->estimated_cost, 0, ',', '.') : '',
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

        $filename = 'Tiket_Permohonan_REQ-' . str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) . '_' . ($clientRequest->vehicle ? str_replace(' ', '_', $clientRequest->vehicle->plate_number) : 'Unit') . '.pdf';
        return $pdf->download($filename);
    }

    // Export Single Client Request to Word Document (.doc)
    public function exportClientRequestDoc(\App\Models\ClientRequest $clientRequest)
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
            if (empty($trimmed)) continue;

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
                $currentComplaint['desc'] .= (!empty($currentComplaint['desc']) ? "\n" : '') . $trimmed;
            } elseif (!str_starts_with($trimmed, '[Kategori:')) {
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
                'cost' => $clientRequest->estimated_cost > 0 ? 'Rp ' . number_format($clientRequest->estimated_cost, 0, ',', '.') : '',
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

        $filename = 'Tiket_Permohonan_REQ-' . str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) . '_' . ($clientRequest->vehicle ? str_replace(' ', '_', $clientRequest->vehicle->plate_number) : 'Unit') . '.doc';

        return response($content, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
