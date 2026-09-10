<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'overview');
        $activeVendorId = session('active_vendor_id');

        // Budget summary
        $budgetQuery = Budget::query();
        $expenseQuery = Expense::with(['vehicle.location', 'vendor']);
        $incomeQuery = Income::with('vendor');

        if ($activeVendorId) {
            $budgetQuery->where('vendor_id', $activeVendorId);
            $expenseQuery->where('vendor_id', $activeVendorId);
            $incomeQuery->where('vendor_id', $activeVendorId);
        }

        $budgets = $budgetQuery->get();
        $totalBudget = $budgets->sum('total_budget_36m');
        $totalExpense = (clone $expenseQuery)->sum('amount');
        $remainingBudget = $totalBudget - $totalExpense;

        // Incomes query & metrics
        $perPageIncomes = in_array((int) $request->get('per_page_incomes', 12), [10, 12, 25, 50, 100]) ? (int) $request->get('per_page_incomes', 12) : 12;
        $incomes = (clone $incomeQuery)->orderBy('invoice_date', 'desc')->paginate($perPageIncomes, ['*'], 'incomes_page')->withQueryString();
        $totalIncomeGross = (clone $incomeQuery)->sum('gross_amount');
        $totalIncomePpn = (clone $incomeQuery)->sum('ppn_amount');
        $totalIncomePph = round($totalIncomeGross * 0.02, 2);
        $totalIncomeNet = round($totalIncomeGross - $totalIncomePph, 2);
        $totalIncomePaid = (clone $incomeQuery)->where('status', 'lunas')->sum('gross_amount');
        $totalIncomeUnpaid = (clone $incomeQuery)->where('status', '!=', 'lunas')->sum('gross_amount');

        // Expenses stats
        $totalExpensesCount = (clone $expenseQuery)->count();
        $totalServiceExpense = (clone $expenseQuery)->where('category', 'service_berkala')->sum('amount');
        $totalTaxExpense = (clone $expenseQuery)->where('category', 'pajak_stnk')->sum('amount');
        $totalOtherExpense = $totalExpense - ($totalServiceExpense + $totalTaxExpense);

        // Expenses filter by category
        if ($request->filled('category')) {
            $expenseQuery->where('category', $request->category);
        }
        $perPageExpenses = in_array((int) $request->get('per_page_expenses', 15), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page_expenses', 15) : 15;
        $expenses = $expenseQuery->orderBy('transaction_date', 'desc')->paginate($perPageExpenses, ['*'], 'expenses_page')->withQueryString();

        // Expense categories breakdown against memo categories
        $categoriesReport = [];
        $categories = [
            'pajak_stnk' => 'Pajak Kendaraan (STNK)',
            'service_berkala' => 'Service Berkala & Pelumas',
            'ban' => 'Penggantian Ban',
            'accu' => 'Penggantian Accu',
            'sparepart' => 'Sparepart & Fast Moving',
            'asuransi' => 'Asuransi Kendaraan',
            'motor_pengganti' => 'Biaya Motor Pengganti',
            'legalitas_kepolisian' => 'Legalitas & Kepolisian',
            'overhead_admin' => 'Transportasi, Administrasi & Operasional',
        ];

        // 1. Calculate detailed spent from Maintenance Records (handles multi-item requests like Service + Ban + Aki)
        $mRecordQuery = MaintenanceRecord::query();
        if ($activeVendorId) {
            $mRecordQuery->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }
        $allMaintenanceRecords = $mRecordQuery->get();

        $maintenanceCategoryCosts = [
            'service_berkala' => 0,
            'ban' => 0,
            'accu' => 0,
            'sparepart' => 0,
            'motor_pengganti' => 0,
            'overhead_admin' => 0,
        ];

        foreach ($allMaintenanceRecords as $rec) {
            $recCost = (float) $rec->cost;
            $complaints = $rec->parsed_complaints;

            if (!empty($complaints) && count($complaints) > 0) {
                $hasParsedCost = false;
                foreach ($complaints as $c) {
                    $itemCost = (float) str_replace(['.', ','], '', $c['cost'] ?? '0');
                    if ($itemCost > 0) {
                        $hasParsedCost = true;
                        $cat = strtolower($c['category'] ?? '');
                        $title = strtolower($c['title'] ?? '');

                        if (str_contains($cat, 'aki') || str_contains($cat, 'accu') || str_contains($title, 'aki') || str_contains($title, 'accu')) {
                            $maintenanceCategoryCosts['accu'] += $itemCost;
                        } elseif (str_contains($cat, 'ban') || str_contains($title, 'ban')) {
                            $maintenanceCategoryCosts['ban'] += $itemCost;
                        } elseif (str_contains($cat, 'service') || str_contains($cat, 'pelumas') || str_contains($cat, 'oli') || str_contains($title, 'service') || str_contains($title, 'oli')) {
                            $maintenanceCategoryCosts['service_berkala'] += $itemCost;
                        } elseif (str_contains($cat, 'motor_pengganti') || str_contains($cat, 'pengganti') || str_contains($title, 'motor pengganti')) {
                            $maintenanceCategoryCosts['motor_pengganti'] += $itemCost;
                        } else {
                            $maintenanceCategoryCosts['sparepart'] += $itemCost;
                        }
                    }
                }

                if (!$hasParsedCost) {
                    $mainCat = match($rec->type) {
                        'ganti_ban' => 'ban',
                        'ganti_accu' => 'accu',
                        'sparepart' => 'sparepart',
                        'motor_pengganti' => 'motor_pengganti',
                        default => 'service_berkala',
                    };
                    $maintenanceCategoryCosts[$mainCat] += $recCost;
                }
            } else {
                $mainCat = match($rec->type) {
                    'ganti_ban' => 'ban',
                    'ganti_accu' => 'accu',
                    'sparepart' => 'sparepart',
                    'motor_pengganti' => 'motor_pengganti',
                    default => 'service_berkala',
                };
                $maintenanceCategoryCosts[$mainCat] += $recCost;
            }
        }

        foreach ($categories as $key => $lbl) {
            $allocQ = Budget::where('category', $key);
            if ($activeVendorId) {
                $allocQ->where('vendor_id', $activeVendorId);
            }

            // For maintenance-related categories, use the granular sub-item calculation
            // For other categories (pajak_stnk, asuransi, legalitas, overhead), sum from Expense table
            if (isset($maintenanceCategoryCosts[$key])) {
                // Also add any direct Expense not tied to maintenance_record_id
                $directExpenseQ = Expense::where('category', $key)->whereNull('maintenance_record_id');
                if ($activeVendorId) $directExpenseQ->where('vendor_id', $activeVendorId);
                $spent = $maintenanceCategoryCosts[$key] + $directExpenseQ->sum('amount');
            } else {
                $spentQ = Expense::where('category', $key);
                if ($activeVendorId) $spentQ->where('vendor_id', $activeVendorId);
                $spent = $spentQ->sum('amount');
            }

            $allocated = $allocQ->sum('total_budget_36m');
            if (in_array($key, ['motor_pengganti', 'legalitas_kepolisian', 'overhead_admin']) && $allocated == 0) {
                $allocated = ($key === 'overhead_admin' || $key === 'motor_pengganti') ? 99000000 : 0;
            }

            $categoriesReport[$key] = [
                'label' => $lbl,
                'spent' => $spent,
                'allocated' => $allocated,
                'remaining' => $allocated > 0 ? ($allocated - $spent) : 0,
            ];
        }

        $budgetList = (clone $budgetQuery)->orderBy('id', 'asc')->get();

        $vehicleQuery = Vehicle::orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();

        return view('financial.index', compact(
            'tab',
            'budgets',
            'budgetList',
            'totalBudget',
            'totalExpense',
            'remainingBudget',
            'incomes',
            'totalIncomeGross',
            'totalIncomePpn',
            'totalIncomePph',
            'totalIncomeNet',
            'totalIncomePaid',
            'totalIncomeUnpaid',
            'totalExpensesCount',
            'totalServiceExpense',
            'totalTaxExpense',
            'totalOtherExpense',
            'expenses',
            'categoriesReport',
            'vehicles',
            'perPageIncomes',
            'perPageExpenses'
        ));
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|unique:incomes,invoice_number',
            'period_month_year' => 'required|string',
            'invoice_date' => 'required|date',
            'client_name' => 'required|string',
            'crf_count' => 'required|integer',
            'supra_count' => 'required|integer',
            'bast_number' => 'nullable|string',
            'status' => 'required|in:draft,tertagih,lunas',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $crfSubtotal = $validated['crf_count'] * 1570000;
        $supraSubtotal = $validated['supra_count'] * 860000;
        $gross = $crfSubtotal + $supraSubtotal;
        $ppn = $gross * 0.11;
        $totalWithPpn = $gross + $ppn;

        Income::create(array_merge($validated, [
            'crf_subtotal' => $crfSubtotal,
            'supra_subtotal' => $supraSubtotal,
            'gross_amount' => $gross,
            'ppn_rate' => 11.00,
            'ppn_amount' => $ppn,
            'total_amount_with_ppn' => $totalWithPpn,
        ]));

        return redirect()->route('financial.index', ['tab' => 'incomes'])->with('success', 'Tagihan sewa berhasil ditambahkan!');
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'transaction_date' => 'required|date',
            'category' => 'required|in:pajak_stnk,service_berkala,ban,accu,sparepart,asuransi,motor_pengganti,legalitas_kepolisian,overhead_admin',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'memo_number' => 'nullable|string',
            'journal_entry_number' => 'nullable|string',
            'recipient_vendor' => 'nullable|string',
            'description' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')->store('expenses', 'public');
        }

        $validated['expense_number'] = 'EXP-' . date('Ymd') . '-' . rand(100, 999);

        if (empty($validated['vendor_id'])) {
            if (!empty($validated['vehicle_id'])) {
                $veh = Vehicle::find($validated['vehicle_id']);
                $validated['vendor_id'] = $veh?->vendor_id;
            } elseif (session('active_vendor_id')) {
                $validated['vendor_id'] = session('active_vendor_id');
            }
        }

        Expense::create($validated);

        return redirect()->route('financial.index', ['tab' => 'expenses'])->with('success', 'Pengeluaran operasional berhasil dicatat!');
    }

    public function updateIncomeStatus(Request $request, Income $income)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,tertagih,lunas',
            'payment_date' => 'nullable|date',
        ]);

        $income->update($validated);

        return redirect()->route('financial.index', ['tab' => 'incomes'])->with('success', 'Status tagihan diperbarui!');
    }

    public function storeBudget(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'label' => 'required|string|max:255',
            'vehicle_group' => 'nullable|string|max:100',
            'qty_units' => 'required|integer|min:1',
            'unit_rate_monthly' => 'required|numeric|min:0',
            'unit_rate_yearly' => 'nullable|numeric|min:0',
            'total_budget_36m' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        if (empty($validated['vendor_id']) && session('active_vendor_id')) {
            $validated['vendor_id'] = session('active_vendor_id');
        }

        Budget::create($validated);

        return redirect()->route('financial.index', ['tab' => 'overview'])->with('success', 'Plafon pos anggaran baru berhasil ditambahkan!');
    }

    public function updateBudget(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'qty_units' => 'required|integer|min:1',
            'unit_rate_monthly' => 'required|numeric|min:0',
            'unit_rate_yearly' => 'nullable|numeric|min:0',
            'total_budget_36m' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $budget->update($validated);

        return redirect()->route('financial.index', ['tab' => 'overview'])->with('success', 'Plafon pos anggaran berhasil diperbarui!');
    }
}
