<?php

use App\Http\Controllers\AdminClientRequestController;
use App\Http\Controllers\ApiIntegrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\HandoverDocumentController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\InsurancePolicyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\TaxInvoiceController;
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

    // Master Clients & Contracts Module (Separated)
    Route::resource('clients', ClientController::class)->except(['create', 'show', 'edit']);
    Route::resource('vendors', VendorController::class)->except(['create', 'show', 'edit']);
    Route::post('/vendors/switch', [VendorController::class, 'switchVendor'])->name('vendors.switch');

    // Locations Module (Wilayah / Kebun per Klien)
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::get('/vendors/{vendor}/locations', [LocationController::class, 'byVendor'])->name('vendors.locations');

    // Assets & Multi-Unit Module (Mobil, Motor, Mesin Fotocopy, Dispenser)
    Route::redirect('/vehicles', '/assets');
    Route::post('/assets/import', [VehicleController::class, 'importExcel'])->name('vehicles.import');
    Route::resource('assets', VehicleController::class)->parameters(['assets' => 'vehicle'])->names('vehicles');

    // BAST Module (Unit Baru, Unit Pengganti, Pengembalian)
    Route::get('/bast', [HandoverDocumentController::class, 'index'])->name('bast.index');
    Route::post('/bast', [HandoverDocumentController::class, 'store'])->name('bast.store');
    Route::get('/bast/{bast}/pdf', [HandoverDocumentController::class, 'exportPdf'])->name('bast.pdf');
    Route::get('/bast/{bast}/preview', [HandoverDocumentController::class, 'previewPdf'])->name('bast.preview');
    Route::post('/bast/{bast}/upload-signed', [HandoverDocumentController::class, 'uploadSigned'])->name('bast.uploadSigned');
    Route::delete('/bast/{bast}', [HandoverDocumentController::class, 'destroy'])->name('bast.destroy');

    // Maintenance Module (Service, Tires, Battery, Spareparts, Replacement Units)
    Route::resource('maintenance', MaintenanceController::class)->except(['edit', 'update']);

    // Documents & Taxes Module (STNK, Pajak 5 Tahunan, Blokir Polda)
    Route::post('/documents/{document}/pay-tax', [DocumentController::class, 'payTax'])->name('documents.payTax');
    Route::resource('documents', DocumentController::class)->except(['edit', 'update', 'show']);

    // Insurance & Loss Claims Module (Polis & Klaim)
    Route::post('/insurance/policies', [InsurancePolicyController::class, 'store'])->name('insurance.policies.store');
    Route::put('/insurance/policies/{policy}', [InsurancePolicyController::class, 'update'])->name('insurance.policies.update');
    Route::delete('/insurance/policies/{policy}', [InsurancePolicyController::class, 'destroy'])->name('insurance.policies.destroy');
    Route::resource('insurance', InsuranceController::class)->except(['edit', 'show']);

    // Financial & Cashflow Module (Incomes, Expenses, Budget Plafond)
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::get('/financial/budget/create', [FinancialController::class, 'createBudget'])->name('financial.budget.create');
    Route::post('/financial/budget', [FinancialController::class, 'storeBudget'])->name('financial.budget.store');
    Route::put('/financial/budget/{budget}', [FinancialController::class, 'updateBudget'])->name('financial.budget.update');
    Route::get('/financial/income/create', [FinancialController::class, 'createIncome'])->name('financial.income.create');
    Route::post('/financial/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store');
    Route::post('/financial/income/generate', [FinancialController::class, 'generateFromContracts'])->name('financial.income.generate');
    Route::patch('/financial/income/{income}/status', [FinancialController::class, 'updateIncomeStatus'])->name('financial.income.updateStatus');
    Route::get('/financial/expense/create', [FinancialController::class, 'createExpense'])->name('financial.expense.create');
    Route::post('/financial/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store');
    Route::delete('/financial/expense/{expense}', [FinancialController::class, 'destroyExpense'])->name('financial.expense.destroy');

    // Faktur Pajak & Coretax Module
    Route::post('/financial/tax-invoices', [TaxInvoiceController::class, 'store'])->name('financial.tax.store');
    Route::get('/financial/tax-invoices/{taxInvoice}/coretax-xml', [TaxInvoiceController::class, 'exportCoretaxXml'])->name('financial.tax.xml');
    Route::delete('/financial/tax-invoices/{taxInvoice}', [TaxInvoiceController::class, 'destroy'])->name('financial.tax.destroy');

    // API & Accounting Integrations
    Route::get('/integrations', [ApiIntegrationController::class, 'index'])->name('integrations.index');
    Route::post('/integrations/tokens', [ApiIntegrationController::class, 'storeToken'])->name('integrations.tokens.store');
    Route::delete('/integrations/tokens/{apiToken}', [ApiIntegrationController::class, 'destroyToken'])->name('integrations.tokens.destroy');

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
        Route::get('/clients-excel', [ExportController::class, 'exportClientsExcel'])->name('clientsExcel');
        Route::get('/clients-pdf', [ExportController::class, 'exportClientsPdf'])->name('clientsPdf');
        Route::get('/vendors-excel', [ExportController::class, 'exportVendorsExcel'])->name('vendorsExcel');
        Route::get('/vendors-pdf', [ExportController::class, 'exportVendorsPdf'])->name('vendorsPdf');
        Route::get('/maintenance-excel', [ExportController::class, 'exportMaintenanceExcel'])->name('maintenanceExcel');
        Route::get('/maintenance-pdf', [ExportController::class, 'exportMaintenancePdf'])->name('maintenancePdf');
        Route::get('/documents-excel', [ExportController::class, 'exportDocumentsExcel'])->name('documentsExcel');
        Route::get('/documents-pdf', [ExportController::class, 'exportDocumentsPdf'])->name('documentsPdf');
        Route::get('/insurance-excel', [ExportController::class, 'exportInsuranceExcel'])->name('insuranceExcel');
        Route::get('/insurance-pdf', [ExportController::class, 'exportInsurancePdf'])->name('insurancePdf');
        Route::get('/persewaan-excel', [ExportController::class, 'exportPersewaanExcel'])->name('persewaanExcel');
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
