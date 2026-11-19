<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('client')->withCount(['vehicles', 'incomes', 'expenses']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('contract_number', 'like', "%{$s}%")
                    ->orWhere('client_name', 'like', "%{$s}%");
            });
        }

        $vendors = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();
        $clients = Client::orderBy('name')->get();

        return view('vendors.index', compact('vendors', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:vendors,code',
            'contract_number' => 'required|string|max:100',
            'contract_date' => 'nullable|date',
            'client_name' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'npwp' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'duration_months' => 'nullable|integer|min:1|max:240',
            'billing_cycle' => 'nullable|in:bulanan,triwulanan,semesteran,tahunan,2_tahunan',
            'payment_method' => 'nullable|in:qris,va,transfer,giro,cash',
            'billing_day' => 'nullable|integer|min:1|max:28',
            'contract_value' => 'nullable|numeric|min:0',
            'main_contract_value' => 'nullable|numeric|min:0',
            'main_monthly_value' => 'nullable|numeric|min:0',
            'additional_contract_value' => 'nullable|numeric|min:0',
            'additional_monthly_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated = $this->normalizeContractFinancials($validated);

        $client = Client::findOrFail($validated['client_id']);
        $validated['name'] = $validated['name'] ?: $client->name;
        $validated['npwp'] = $validated['npwp'] ?: $client->npwp;
        $validated['pic_name'] = $validated['pic_name'] ?: $client->pic_name;
        $validated['phone'] = $validated['phone'] ?: $client->phone;
        $validated['email'] = $validated['email'] ?: $client->email;

        Vendor::create($validated);

        return redirect()->route('vendors.index')->with('success', 'Data kontrak baru berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:vendors,code,'.$vendor->id,
            'contract_number' => 'required|string|max:100',
            'contract_date' => 'nullable|date',
            'client_name' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'npwp' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'duration_months' => 'nullable|integer|min:1|max:240',
            'billing_cycle' => 'nullable|in:bulanan,triwulanan,semesteran,tahunan,2_tahunan',
            'payment_method' => 'nullable|in:qris,va,transfer,giro,cash',
            'billing_day' => 'nullable|integer|min:1|max:28',
            'contract_value' => 'nullable|numeric|min:0',
            'main_contract_value' => 'nullable|numeric|min:0',
            'main_monthly_value' => 'nullable|numeric|min:0',
            'additional_contract_value' => 'nullable|numeric|min:0',
            'additional_monthly_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated = $this->normalizeContractFinancials($validated);

        $validated = $this->normalizeContractFinancials($validated);

        $client = Client::findOrFail($validated['client_id']);
        $validated['name'] = $validated['name'] ?: $client->name;
        $validated['npwp'] = $validated['npwp'] ?: $client->npwp;

        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Data kontrak berhasil diperbarui.');
    }

    private function normalizeContractFinancials(array $validated): array
    {
        $main = (float) ($validated['main_contract_value'] ?? 0);
        $additional = (float) ($validated['additional_contract_value'] ?? 0);

        $validated['main_contract_value'] = $main;
        $validated['main_monthly_value'] = (float) ($validated['main_monthly_value'] ?? 0);
        $validated['additional_contract_value'] = $additional;
        $validated['additional_monthly_value'] = (float) ($validated['additional_monthly_value'] ?? 0);

        if (empty($validated['contract_value']) && ($main > 0 || $additional > 0)) {
            $validated['contract_value'] = $main + $additional;
        }

        return $validated;
    }

    public function destroy(Vendor $vendor)
    {
        $vehicleCount = $vendor->vehicles()->count();
        if ($vehicleCount > 0) {
            $vendor->vehicles()->update([
                'vendor_id' => null,
                'location_id' => null,
                'status' => 'standby',
            ]);
        }

        if (session('active_vendor_id') == $vendor->id) {
            session()->forget('active_vendor_id');
        }

        $vendor->delete();

        $message = $vehicleCount > 0
            ? "Kontrak berhasil dihapus dan {$vehicleCount} unit kendaraan kini berstatus Standby di Pool."
            : 'Kontrak berhasil dihapus.';

        return redirect()->route('vendors.index')->with('success', $message);
    }

    // Switch active vendor filter in session
    public function switchVendor(Request $request)
    {
        $vendorId = $request->input('vendor_id');
        if ($vendorId === 'all' || empty($vendorId)) {
            session()->forget('active_vendor_id');
        } else {
            session(['active_vendor_id' => (int) $vendorId]);
        }

        return back()->with('success', 'Filter kontrak aktif berhasil diperbarui.');
    }
}
