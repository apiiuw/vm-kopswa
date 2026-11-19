<?php

namespace App\Http\Controllers;

use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index(Request $request)
    {
        $query = InsuranceClaim::with(['vehicle.location', 'vehicle.vendor']);
        $activeVendorId = session('active_vendor_id');

        if ($activeVendorId) {
            $query->whereHas('vehicle', function ($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }

        $perPage = in_array((int) $request->get('per_page', 10), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 10) : 10;
        $claims = $query->orderBy('incident_date', 'desc')->paginate($perPage)->withQueryString();

        $vehicleQuery = Vehicle::orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();

        $statsQuery = InsuranceClaim::query();
        if ($activeVendorId) {
            $statsQuery->whereHas('vehicle', function ($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }

        $stats = [
            'total_claims' => (clone $statsQuery)->count(),
            'total_amount' => (clone $statsQuery)->sum('claim_amount'),
            'settled_amount' => (clone $statsQuery)->sum('settlement_amount'),
            'pending_count' => (clone $statsQuery)->whereIn('status', ['draft', 'diajukan', 'diverifikasi'])->count(),
        ];

        $policyQuery = InsurancePolicy::with(['vehicle', 'vendor'])->orderBy('end_date', 'asc');
        if ($activeVendorId) {
            $policyQuery->where('vendor_id', $activeVendorId);
        }
        $policies = $policyQuery->get();

        $policyStats = [
            'total' => $policies->count(),
            'active' => $policies->where('status', 'active')->count(),
            'expiring_soon' => $policies->where('status', 'active')->filter(fn ($p) => $p->end_date && $p->end_date->lte(now()->addDays(45)))->count(),
            'total_premium' => $policies->sum('premium_amount'),
        ];

        $vendors = Vendor::orderBy('name')->get();
        $tab = $request->get('tab', 'policies');

        return view('insurance.index', compact('claims', 'vehicles', 'stats', 'perPage', 'policies', 'policyStats', 'vendors', 'tab'));
    }

    public function create()
    {
        $activeVendorId = session('active_vendor_id');
        $vehicleQuery = Vehicle::orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();

        return view('insurance.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'claim_type' => 'required|in:tlo_kehilangan,partial_loss,kerusakan_berat',
            'incident_date' => 'required|date',
            'police_report_number' => 'nullable|string',
            'polda_blocking_number' => 'nullable|string',
            'claim_amount' => 'required|numeric|min:0',
            'settlement_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,diajukan,diverifikasi,disetujui,cair,ditolak',
            'incident_chronology' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'driver_sim_number' => 'nullable|string',
            'claim_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('claim_file')) {
            $validated['claim_file'] = $request->file('claim_file')->store('insurance_claims', 'public');
        }

        InsuranceClaim::create($validated);

        // Update vehicle status if lost
        if ($validated['claim_type'] === 'tlo_kehilangan') {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'lost']);
        }

        return redirect()->route('insurance.index')->with('success', 'Klaim asuransi berhasil dicatat!');
    }

    public function update(Request $request, InsuranceClaim $insurance)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,diajukan,diverifikasi,disetujui,cair,ditolak',
            'settlement_amount' => 'nullable|numeric|min:0',
            'polda_blocking_number' => 'nullable|string',
            'incident_chronology' => 'nullable|string',
        ]);

        $insurance->update($validated);

        if ($validated['status'] === 'cair') {
            $insurance->vehicle()->update(['status' => 'claimed']);
        }

        return redirect()->route('insurance.index')->with('success', 'Status klaim asuransi berhasil diperbarui!');
    }

    public function destroy(InsuranceClaim $insurance)
    {
        $insurance->delete();

        return redirect()->route('insurance.index')->with('success', 'Data klaim berhasil dihapus!');
    }
}
