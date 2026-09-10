<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MaintenanceRecord;
use App\Models\VehicleDocument;
use App\Models\InsuranceClaim;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect client users to client portal dashboard
        if (Auth::check() && Auth::user()->category === 'klien') {
            return redirect()->route('client.dashboard');
        }

        $activeVendorId = session('active_vendor_id');

        // 1. Vehicle Counts
        $vehicleQuery = Vehicle::query();
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }

        $totalVehicles = (clone $vehicleQuery)->count();
        $crfCount = (clone $vehicleQuery)->where('vehicle_type', 'honda_crf_150')->count();
        $supraCount = (clone $vehicleQuery)->where('vehicle_type', 'honda_supra_x_125')->count();
        $activeCount = (clone $vehicleQuery)->where('status', 'active')->count();
        $maintenanceCount = (clone $vehicleQuery)->where('status', 'maintenance')->count();
        $replacedCount = (clone $vehicleQuery)->where('status', 'replaced')->count();
        $lostCount = (clone $vehicleQuery)->where('status', 'lost')->count();

        // 2. Financial Metrics
        $budgetQuery = Budget::query();
        $expenseQuery = Expense::query();
        $incomeQuery = Income::query();

        if ($activeVendorId) {
            $budgetQuery->where('vendor_id', $activeVendorId);
            $expenseQuery->where('vendor_id', $activeVendorId);
            $incomeQuery->where('vendor_id', $activeVendorId);
        }

        $totalBudgetOperasional = (clone $budgetQuery)->sum('total_budget_36m');
        $totalExpense = (clone $expenseQuery)->sum('amount');
        $sisaBudgetOperasional = $totalBudgetOperasional - $totalExpense;

        $totalIncomeGross = (clone $incomeQuery)->sum('gross_amount');
        $totalIncomePaid = (clone $incomeQuery)->where('status', 'lunas')->sum('gross_amount');
        $totalIncomePending = (clone $incomeQuery)->where('status', 'tertagih')->sum('gross_amount');

        // Estimated total contract: sum of monthly rent * 36 or from incomes
        $totalContractValue = (clone $vehicleQuery)->sum('rental_price_per_month') * 36;
        if ($totalContractValue == 0) {
            $totalContractValue = 4142160000;
        }

        // 3. Category Budgets & Expenses Breakdown
        $budgetByCategory = [
            'service_berkala' => (clone $budgetQuery)->where('category', 'service_berkala')->sum('total_budget_36m') ?: 219000000,
            'ban' => (clone $budgetQuery)->where('category', 'ban')->sum('total_budget_36m') ?: 164700000,
            'accu' => (clone $budgetQuery)->where('category', 'accu')->sum('total_budget_36m') ?: 45000000,
            'sparepart' => (clone $budgetQuery)->where('category', 'sparepart')->sum('total_budget_36m') ?: 129360000,
            'motor_pengganti' => (clone $budgetQuery)->where('category', 'motor_pengganti')->sum('total_budget_36m') ?: 99000000,
            'pajak_stnk' => (clone $budgetQuery)->where('category', 'pajak_stnk')->sum('total_budget_36m') ?: 172500000,
            'asuransi' => (clone $budgetQuery)->where('category', 'asuransi')->sum('total_budget_36m') ?: 210300000,
            'legalitas_kepolisian' => (clone $budgetQuery)->where('category', 'legalitas_kepolisian')->sum('total_budget_36m') ?: 10000000,
            'overhead_admin' => (clone $budgetQuery)->where('category', 'overhead_admin')->sum('total_budget_36m') ?: 18000000,
        ];

        $expensesByCategory = [
            'service_berkala' => (clone $expenseQuery)->where('category', 'service_berkala')->sum('amount'),
            'ban' => (clone $expenseQuery)->where('category', 'ban')->sum('amount'),
            'accu' => (clone $expenseQuery)->where('category', 'accu')->sum('amount'),
            'sparepart' => (clone $expenseQuery)->where('category', 'sparepart')->sum('amount'),
            'motor_pengganti' => (clone $expenseQuery)->where('category', 'motor_pengganti')->sum('amount'),
            'pajak_stnk' => (clone $expenseQuery)->where('category', 'pajak_stnk')->sum('amount'),
            'asuransi' => (clone $expenseQuery)->where('category', 'asuransi')->sum('amount'),
            'legalitas_kepolisian' => (clone $expenseQuery)->where('category', 'legalitas_kepolisian')->sum('amount'),
            'overhead_admin' => (clone $expenseQuery)->where('category', 'overhead_admin')->sum('amount'),
        ];

        // 4. Alerts: Documents Expiring within 60 Days
        $expiringDocsQuery = VehicleDocument::with(['vehicle.location', 'vehicle.vendor'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays(60));
        if ($activeVendorId) {
            $expiringDocsQuery->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }
        $expiringDocs = $expiringDocsQuery->orderBy('expiry_date', 'asc')->take(6)->get();

        // 5. Recent Maintenances
        $recentMaintenancesQuery = MaintenanceRecord::with(['vehicle.location', 'vehicle.vendor']);
        if ($activeVendorId) {
            $recentMaintenancesQuery->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }
        $recentMaintenances = $recentMaintenancesQuery->orderBy('date', 'desc')->take(5)->get();

        // 6. Recent Incomes & Claims
        $recentIncomes = (clone $incomeQuery)->orderBy('invoice_date', 'desc')->take(4)->get();

        $claimsQuery = InsuranceClaim::with(['vehicle.vendor']);
        if ($activeVendorId) {
            $claimsQuery->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }
        $recentClaims = $claimsQuery->orderBy('incident_date', 'desc')->take(3)->get();

        // 7. Monthly Cashflow Data for Chart
        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $dateIncomeFormat = $driver === 'sqlite' ? "strftime('%Y-%m', invoice_date)" : "DATE_FORMAT(invoice_date, '%Y-%m')";
        $dateExpenseFormat = $driver === 'sqlite' ? "strftime('%Y-%m', transaction_date)" : "DATE_FORMAT(transaction_date, '%Y-%m')";

        $monthlyIncomes = (clone $incomeQuery)->selectRaw("{$dateIncomeFormat} as month, SUM(gross_amount) as total")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyExpenses = (clone $expenseQuery)->selectRaw("{$dateExpenseFormat} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month')
            ->toArray();

        // Combine unique months
        $allMonths = array_values(array_unique(array_merge(array_keys($monthlyIncomes), array_keys($monthlyExpenses))));
        sort($allMonths);

        $chartLabels = [];
        $chartIncomeData = [];
        $chartExpenseData = [];

        foreach ($allMonths as $m) {
            $chartLabels[] = Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y');
            $chartIncomeData[] = (float)($monthlyIncomes[$m] ?? 0);
            $chartExpenseData[] = (float)($monthlyExpenses[$m] ?? 0);
        }

        // 8. Location Vehicle Distribution
        $locations = \App\Models\Location::withCount(['vehicles' => function($q) use ($activeVendorId) {
            if ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            }
        }])->get();
        $locationLabels = $locations->pluck('name')->toArray();
        $locationCounts = $locations->pluck('vehicles_count')->toArray();

        // 9. Maintenance Cost Monthly Trend
        $maintMonthly = (clone $expenseQuery)->whereIn('category', ['service_berkala', 'ban', 'accu', 'sparepart', 'motor_pengganti'])
            ->selectRaw("{$dateExpenseFormat} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month')
            ->toArray();
        
        $maintTrendLabels = [];
        $maintTrendData = [];
        foreach ($allMonths as $m) {
            $maintTrendLabels[] = Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y');
            $maintTrendData[] = (float)($maintMonthly[$m] ?? 0);
        }

        // 10. Comparative Absorption Analytics (Budget vs Actual per Category)
        $comparisonCategories = [
            'service_berkala' => 'Service & Pelumas',
            'ban' => 'Penggantian Ban',
            'accu' => 'Penggantian Accu',
            'sparepart' => 'Suku Cadang',
            'motor_pengganti' => 'Unit Pengganti',
            'pajak_stnk' => 'Pajak STNK',
            'asuransi' => 'Asuransi (TLO)',
            'overhead_admin' => 'Admin & Operasional'
        ];
        $compareLabels = [];
        $compareBudgetData = [];
        $compareExpenseData = [];
        $compareAbsorptionPercent = [];

        foreach ($comparisonCategories as $key => $label) {
            $b = (float)($budgetByCategory[$key] ?? 0);
            $e = (float)($expensesByCategory[$key] ?? 0);
            $compareLabels[] = $label;
            $compareBudgetData[] = $b;
            $compareExpenseData[] = $e;
            $compareAbsorptionPercent[] = $b > 0 ? round(($e / $b) * 100, 1) : 0;
        }

        // 11. Request Status Breakdown
        $requestStatusCounts = [
            'menunggu_review' => \App\Models\ClientRequest::when($activeVendorId, fn($q) => $q->where('vendor_id', $activeVendorId))->where('status', 'menunggu_review')->count(),
            'diproses' => \App\Models\ClientRequest::when($activeVendorId, fn($q) => $q->where('vendor_id', $activeVendorId))->where('status', 'diproses')->count(),
            'unit_dikirim' => \App\Models\ClientRequest::when($activeVendorId, fn($q) => $q->where('vendor_id', $activeVendorId))->where('status', 'unit_dikirim')->count(),
            'selesai' => \App\Models\ClientRequest::when($activeVendorId, fn($q) => $q->where('vendor_id', $activeVendorId))->where('status', 'selesai')->count(),
            'ditolak' => \App\Models\ClientRequest::when($activeVendorId, fn($q) => $q->where('vendor_id', $activeVendorId))->where('status', 'ditolak')->count(),
        ];

        // 12. Top 5 Costliest Maintenance Vehicles
        $topVehicleCosts = MaintenanceRecord::selectRaw('vehicle_id, SUM(cost) as total_spent, COUNT(id) as total_records')
            ->whereNotNull('vehicle_id')
            ->when($activeVendorId, function($q) use ($activeVendorId) {
                $q->whereHas('vehicle', fn($v) => $v->where('vendor_id', $activeVendorId));
            })
            ->groupBy('vehicle_id')
            ->orderByDesc('total_spent')
            ->take(5)
            ->with(['vehicle.location'])
            ->get();

        $topVehicleLabels = [];
        $topVehicleData = [];
        foreach ($topVehicleCosts as $tvc) {
            $topVehicleLabels[] = ($tvc->vehicle ? $tvc->vehicle->plate_number : 'ID #' . $tvc->vehicle_id) . ' (' . ($tvc->vehicle ? $tvc->vehicle->model_name : '') . ')';
            $topVehicleData[] = (float)$tvc->total_spent;
        }

        // 13. Recent Client Requests
        $clientRequestsQuery = \App\Models\ClientRequest::with(['vehicle', 'vendor', 'user']);
        if ($activeVendorId) {
            $clientRequestsQuery->where('vendor_id', $activeVendorId);
        }
        $recentClientRequests = $clientRequestsQuery->latest()->take(5)->get();
        $pendingClientRequestsCount = \App\Models\ClientRequest::where('status', 'menunggu_review')->count();

        return view('dashboard.index', compact(
            'totalVehicles',
            'crfCount',
            'supraCount',
            'activeCount',
            'maintenanceCount',
            'replacedCount',
            'lostCount',
            'totalContractValue',
            'totalBudgetOperasional',
            'totalExpense',
            'sisaBudgetOperasional',
            'totalIncomeGross',
            'totalIncomePaid',
            'totalIncomePending',
            'budgetByCategory',
            'expensesByCategory',
            'expiringDocs',
            'recentMaintenances',
            'recentIncomes',
            'recentClaims',
            'chartLabels',
            'chartIncomeData',
            'chartExpenseData',
            'locationLabels',
            'locationCounts',
            'maintTrendLabels',
            'maintTrendData',
            'compareLabels',
            'compareBudgetData',
            'compareExpenseData',
            'compareAbsorptionPercent',
            'requestStatusCounts',
            'topVehicleCosts',
            'topVehicleLabels',
            'topVehicleData',
            'recentClientRequests',
            'pendingClientRequestsCount'
        ));
    }
}
