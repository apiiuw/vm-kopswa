<?php

namespace App\Http\Controllers;

use App\Models\InsurancePolicy;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class InsurancePolicyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'policy_number' => 'required|string|max:120',
            'insurance_company' => 'required|string|max:150',
            'coverage_type' => 'required|in:all_risk,tlo,kombinasi',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'premium_amount' => 'required|numeric|min:0',
            'sum_insured' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,expired,cancelled',
            'policy_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['vendor_id']) && ! empty($validated['vehicle_id'])) {
            $validated['vendor_id'] = Vehicle::where('id', $validated['vehicle_id'])->value('vendor_id');
        } elseif (empty($validated['vendor_id']) && session('active_vendor_id')) {
            $validated['vendor_id'] = session('active_vendor_id');
        }

        if ($request->hasFile('policy_file')) {
            $validated['policy_file'] = $request->file('policy_file')->store('insurance_policies', 'public');
        }

        $validated['sum_insured'] = $validated['sum_insured'] ?? 0;
        InsurancePolicy::create($validated);

        return redirect()->route('insurance.index', ['tab' => 'policies'])->with('success', 'Data Polis Asuransi berhasil disimpan!');
    }

    public function update(Request $request, InsurancePolicy $policy)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,expired,cancelled',
            'end_date' => 'nullable|date',
            'premium_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $policy->update(array_filter($validated, fn ($v) => $v !== null));

        return redirect()->route('insurance.index', ['tab' => 'policies'])->with('success', 'Data Polis Asuransi berhasil diperbarui!');
    }

    public function destroy(InsurancePolicy $policy)
    {
        $policy->delete();

        return redirect()->route('insurance.index', ['tab' => 'policies'])->with('success', 'Data Polis Asuransi berhasil dihapus.');
    }
}
