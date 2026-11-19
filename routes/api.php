<?php

use App\Http\Controllers\Api\AccountingApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/accounting')->name('api.accounting.')->group(function () {
    Route::get('/summary', [AccountingApiController::class, 'summary'])->name('summary');
    Route::get('/incomes', [AccountingApiController::class, 'incomes'])->name('incomes');
    Route::get('/expenses', [AccountingApiController::class, 'expenses'])->name('expenses');
    Route::get('/depreciation', [AccountingApiController::class, 'depreciation'])->name('depreciation');
});
