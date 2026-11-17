<?php

use App\Http\Controllers\AdminClientRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Entrypoint Dashboard (automatically redirects klien users to Client Portal)
Route::middleware(['auth'])->get('/', [DashboardController::class, 'index'])->name('dashboard');

// Internal Protected Routes (Only internal staff)
Route::middleware(['auth', 'category:internal'])->group(function () {

    // Vendors Module (Multi-Vendor)
    Route::resource('vendors', VendorController::class)->except(['create', 'show', 'edit']);
    Route::post('/vendors/switch', [VendorController::class, 'switchVendor'])->name('vendors.switch');

    // Locations Module (Wilayah / Kebun per Klien)
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::get('/vendors/{vendor}/locations', [LocationController::class, 'byVendor'])->name('vendors.locations');

    // Vehicles Module
    Route::resource('vehicles', VehicleController::class);

    // Maintenance Module (Service, Tires, Battery, Spareparts, Replacement Units)
    Route::resource('maintenance', MaintenanceController::class)->except(['edit', 'update']);

    // Documents & Taxes Module (STNK, Pajak 5 Tahunan, Blokir Polda)
    Route::post('/documents/{document}/pay-tax', [DocumentController::class, 'payTax'])->name('documents.payTax');
    Route::resource('documents', DocumentController::class)->except(['edit', 'update', 'show']);

    // Insurance & Loss Claims Module
    Route::resource('insurance', InsuranceController::class)->except(['edit', 'show']);

    // Financial & Cashflow Module (Incomes, Expenses, Budget Plafond)
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::get('/financial/budget/create', [FinancialController::class, 'createBudget'])->name('financial.budget.create');
    Route::post('/financial/budget', [FinancialController::class, 'storeBudget'])->name('financial.budget.store');
    Route::put('/financial/budget/{budget}', [FinancialController::class, 'updateBudget'])->name('financial.budget.update');
    Route::get('/financial/income/create', [FinancialController::class, 'createIncome'])->name('financial.income.create');
    Route::post('/financial/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store');
    Route::patch('/financial/income/{income}/status', [FinancialController::class, 'updateIncomeStatus'])->name('financial.income.updateStatus');
    Route::get('/financial/expense/create', [FinancialController::class, 'createExpense'])->name('financial.expense.create');
    Route::post('/financial/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store');
    Route::delete('/financial/expense/{expense}', [FinancialController::class, 'destroyExpense'])->name('financial.expense.destroy');

    // Export Reports (PDF & Excel)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/budget-pdf', [ExportController::class, 'exportBudgetPdf'])->name('budgetPdf');
        Route::get('/vehicles-pdf', [ExportController::class, 'exportVehiclesPdf'])->name('vehiclesPdf');
        Route::get('/vehicle/{vehicle}/card-pdf', [ExportController::class, 'exportVehicleHistoryPdf'])->name('vehicleCardPdf');
        Route::get('/client-request/{clientRequest}/pdf', [ExportController::class, 'exportClientRequestPdf'])->name('clientRequestPdf');
        Route::get('/client-request/{clientRequest}/doc', [ExportController::class, 'exportClientRequestDoc'])->name('clientRequestDoc');
        Route::get('/invoice/{income}/pdf', [ExportController::class, 'exportSingleInvoicePdf'])->name('singleInvoicePdf');
        Route::get('/financial-excel', [ExportController::class, 'exportFinancialExcel'])->name('financialExcel');
        Route::get('/vehicles-excel', [ExportController::class, 'exportVehiclesExcel'])->name('vehiclesExcel');
    });

    // Internal Users Management
    Route::get('/users', [AuthController::class, 'usersIndex'])->name('users.index');
    Route::get('/users/create', [AuthController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AuthController::class, 'storeUser'])->name('users.store');
    Route::delete('/users/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');

    // Admin Management for Client Requests (Layanan & Tiket Klien)
    Route::prefix('admin/client-requests')->name('admin.client-requests.')->group(function () {
        Route::get('/', [AdminClientRequestController::class, 'index'])->name('index');
        Route::get('/{clientRequest}', [AdminClientRequestController::class, 'show'])->name('show');
        Route::patch('/{clientRequest}/status', [AdminClientRequestController::class, 'updateStatus'])->name('updateStatus');
    });
});

// Client Portal Routes (Top Navigation, No Sidebar, Realtime Service & Loss Requests)
Route::middleware(['auth', 'category:klien'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/requests', [ClientPortalController::class, 'indexRequests'])->name('requests.index');
    Route::get('/requests/create', [ClientPortalController::class, 'createRequest'])->name('requests.create');
    Route::post('/requests', [ClientPortalController::class, 'storeRequest'])->name('requests.store');
    Route::get('/requests/{clientRequest}', [ClientPortalController::class, 'showRequest'])->name('requests.show');
    Route::get('/requests-realtime', [ClientPortalController::class, 'realtimeStatusJson'])->name('requests.realtime');
    Route::get('/vehicles', [ClientPortalController::class, 'vehiclesIndex'])->name('vehicles.index');
});
