<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\Expense;
use App\Models\Income;
use App\Models\TaxInvoice;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountingApiController extends Controller
{
    private function authenticate(Request $request): ?JsonResponse
    {
        $bearer = $request->bearerToken() ?: $request->header('X-API-KEY');
        if (! $bearer) {
            return response()->json(['error' => 'Unauthorized: Missing API Token'], 401);
        }

        $token = ApiToken::where('token_hash', hash('sha256', $bearer))
            ->where('is_active', true)
            ->first();

        if (! $token) {
            return response()->json(['error' => 'Unauthorized: Invalid or inactive API Token'], 401);
        }

        $token->update(['last_used_at' => now()]);

        return null;
    }

    public function summary(Request $request): JsonResponse
    {
        if ($authError = $this->authenticate($request)) {
            return $authError;
        }

        $vehicles = Vehicle::all();

        return response()->json([
            'status' => 'ok',
            'company' => 'Koperasi Swadharma',
            'generated_at' => now()->toIso8601String(),
            'data' => [
                'total_assets' => $vehicles->count(),
                'total_acquisition_value' => round((float) $vehicles->sum('acquisition_value'), 2),
                'total_book_value' => round((float) $vehicles->sum('book_value'), 2),
                'total_incomes_gross' => round((float) Income::sum('gross_amount'), 2),
                'total_incomes_paid' => round((float) Income::where('status', 'lunas')->sum('total_amount_with_ppn'), 2),
                'total_expenses' => round((float) Expense::sum('amount'), 2),
                'total_tax_invoices' => TaxInvoice::count(),
            ],
        ]);
    }

    public function incomes(Request $request): JsonResponse
    {
        if ($authError = $this->authenticate($request)) {
            return $authError;
        }

        $incomes = Income::with(['vendor', 'taxInvoice'])
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->map(fn (Income $inc) => [
                'id' => $inc->id,
                'invoice_number' => $inc->invoice_number,
                'contract_number' => $inc->contract_number ?? $inc->vendor?->contract_number,
                'income_type' => $inc->income_type ?? 'sewa',
                'billing_cycle' => $inc->billing_cycle ?? 'bulanan',
                'client_name' => $inc->client_name,
                'invoice_date' => $inc->invoice_date?->toDateString(),
                'due_date' => $inc->due_date?->toDateString(),
                'gross_amount' => (float) $inc->gross_amount,
                'ppn_amount' => (float) $inc->ppn_amount,
                'total_amount_with_ppn' => (float) $inc->total_amount_with_ppn,
                'paid_amount' => (float) $inc->paid_amount,
                'status' => $inc->status,
                'payment_date' => $inc->payment_date?->toDateString(),
                'faktur_number' => $inc->taxInvoice?->faktur_number,
            ]);

        return response()->json(['status' => 'ok', 'data' => $incomes]);
    }

    public function expenses(Request $request): JsonResponse
    {
        if ($authError = $this->authenticate($request)) {
            return $authError;
        }

        $expenses = Expense::with(['vehicle', 'vendor'])
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->map(fn (Expense $exp) => [
                'id' => $exp->id,
                'expense_number' => $exp->expense_number,
                'contract_number' => $exp->vendor?->contract_number,
                'plate_or_asset_code' => $exp->vehicle?->plate_number,
                'transaction_date' => $exp->transaction_date?->toDateString(),
                'category' => $exp->category,
                'title' => $exp->title,
                'amount' => (float) $exp->amount,
                'recipient_vendor' => $exp->recipient_vendor,
            ]);

        return response()->json(['status' => 'ok', 'data' => $expenses]);
    }

    public function depreciation(Request $request): JsonResponse
    {
        if ($authError = $this->authenticate($request)) {
            return $authError;
        }

        $assets = Vehicle::with('vendor')
            ->get()
            ->map(fn (Vehicle $v) => [
                'id' => $v->id,
                'asset_type' => $v->asset_type ?? 'motor',
                'plate_or_asset_code' => $v->plate_number,
                'model_name' => $v->model_name,
                'contract_number' => $v->vendor?->contract_number,
                'status' => $v->status,
                'acquisition_date' => $v->acquisition_date?->toDateString(),
                'invoice_value' => (float) $v->invoice_value,
                'acquisition_value' => (float) $v->acquisition_value,
                'monthly_depreciation' => $v->monthly_depreciation,
                'accumulated_depreciation' => $v->accumulated_depreciation,
                'book_value' => $v->book_value,
                'sold_date' => $v->sold_date?->toDateString(),
                'sold_price' => $v->sold_price ? (float) $v->sold_price : null,
            ]);

        return response()->json(['status' => 'ok', 'data' => $assets]);
    }
}
