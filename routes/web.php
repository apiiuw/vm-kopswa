<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Internal Protected Routes (All internal roles have equal full permissions)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Vendors Module (Multi-Vendor)
    Route::resource('vendors', VendorController::class)->except(['create', 'show', 'edit']);
    Route::post('/vendors/switch', [VendorController::class, 'switchVendor'])->name('vendors.switch');

    // Locations Module (Wilayah / Kebun per Klien)
    Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->name('locations.destroy');
    Route::get('/vendors/{vendor}/locations', [\App\Http\Controllers\LocationController::class, 'byVendor'])->name('vendors.locations');

    // Vehicles Module
    Route::resource('vehicles', VehicleController::class);

    // Maintenance Module (Service, Tires, Battery, Spareparts, Replacement Units)
    Route::resource('maintenance', MaintenanceController::class)->except(['edit', 'update']);

    // Documents & Taxes Module (STNK, Pajak 5 Tahunan, Blokir Polda)
    Route::post('/documents/{document}/pay-tax', [DocumentController::class, 'payTax'])->name('documents.payTax');
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);

    // Insurance & Loss Claims Module
    Route::resource('insurance', InsuranceController::class)->except(['edit']);

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
        Route::get('/', [\App\Http\Controllers\AdminClientRequestController::class, 'index'])->name('index');
        Route::get('/{clientRequest}', [\App\Http\Controllers\AdminClientRequestController::class, 'show'])->name('show');
        Route::patch('/{clientRequest}/status', [\App\Http\Controllers\AdminClientRequestController::class, 'updateStatus'])->name('updateStatus');
    });

    // Client Portal Routes (Top Navigation, No Sidebar, Realtime Service & Loss Requests)
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ClientPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/requests', [\App\Http\Controllers\ClientPortalController::class, 'indexRequests'])->name('requests.index');
        Route::get('/requests/create', [\App\Http\Controllers\ClientPortalController::class, 'createRequest'])->name('requests.create');
        Route::post('/requests', [\App\Http\Controllers\ClientPortalController::class, 'storeRequest'])->name('requests.store');
        Route::get('/requests/{clientRequest}', [\App\Http\Controllers\ClientPortalController::class, 'showRequest'])->name('requests.show');
        Route::get('/requests-realtime', [\App\Http\Controllers\ClientPortalController::class, 'realtimeStatusJson'])->name('requests.realtime');
        Route::get('/vehicles', [\App\Http\Controllers\ClientPortalController::class, 'vehiclesIndex'])->name('vehicles.index');
    });
});
