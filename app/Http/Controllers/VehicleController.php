<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Location;
use App\Models\Vendor;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['vendor', 'location', 'documents', 'maintenanceRecords']);

        $activeVendorId = session('active_vendor_id');
        if ($activeVendorId) {
            $query->where('vendor_id', $activeVendorId);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('plate_number', 'like', "%{$s}%")
                  ->orWhere('user_holder', 'like', "%{$s}%")
                  ->orWhere('brand', 'like', "%{$s}%")
                  ->orWhere('model_name', 'like', "%{$s}%")
                  ->orWhere('type_name', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('frame_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('type')) {
            $query->where('vehicle_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $perPage = in_array((int) $request->get('per_page', 15), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 15) : 15;
        $vehicles = $query->orderBy('id', 'asc')->paginate($perPage)->withQueryString();
        $locations = Location::all();
        $vendors = Vendor::all();
        $categories = Vehicle::distinct()->whereNotNull('category')->pluck('category')->filter()->values();
        $brands = Vehicle::distinct()->whereNotNull('brand')->pluck('brand')->filter()->values();
        $currentVendor = $activeVendorId ? Vendor::find($activeVendorId) : null;

        $statsQuery = Vehicle::query();
        if ($activeVendorId) {
            $statsQuery->where('vendor_id', $activeVendorId);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('status', 'active')->count(),
            'maintenance' => (clone $statsQuery)->where('status', 'maintenance')->count(),
            'replaced' => (clone $statsQuery)->where('status', 'replaced')->count(),
            'lost' => (clone $statsQuery)->where('status', 'lost')->count(),
            'total_rental' => (clone $statsQuery)->sum('rental_price_per_month'),
        ];

        return view('vehicles.index', compact('vehicles', 'locations', 'vendors', 'categories', 'brands', 'currentVendor', 'activeVendorId', 'perPage', 'stats'));
    }

    public function create()
    {
        $locations = Location::all();
        $vendors = Vendor::all();
        $replacementVehicles = Vehicle::where('status', 'replaced')->orderBy('plate_number')->get();
        return view('vehicles.create', compact('locations', 'vendors', 'replacementVehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'category' => 'required|string|max:100',
            'brand' => 'required|string|max:100',
            'vehicle_type' => 'nullable|string',
            'model_name' => 'required|string|max:150',
            'type_name' => 'nullable|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
            'user_holder' => 'nullable|string',
            'driver_phone' => 'nullable|string|max:50',
            'frame_number' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'year' => 'required|numeric|min:2000|max:2035',
            'color' => 'nullable|string',
            'tax_annual_date' => 'nullable|date',
            'tax_5year_date' => 'nullable|date',
            'rental_price_per_month' => 'required|numeric',
            'status' => 'required|in:active,maintenance,replaced,lost,claimed',
            'replacement_plate_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'stnk_number' => 'nullable|string|max:100',
            'bpkb_number' => 'nullable|string|max:100',
            'stnk_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'bpkb_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        if (empty($validated['vehicle_type'])) {
            $validated['vehicle_type'] = (stripos($validated['model_name'], 'supra') !== false) ? 'honda_supra_x_125' : 'honda_crf_150';
        }

        if (empty($validated['vendor_id']) && session('active_vendor_id')) {
            $validated['vendor_id'] = session('active_vendor_id');
        }

        if ($request->hasFile('stnk_file')) {
            $validated['stnk_file'] = $request->file('stnk_file')->store('vehicles/documents', 'public');
        }

        if ($request->hasFile('bpkb_file')) {
            $validated['bpkb_file'] = $request->file('bpkb_file')->store('vehicles/documents', 'public');
        }

        $vehicle = Vehicle::create($validated);

        // Synchronize STNK document
        if (!empty($validated['stnk_file']) || !empty($validated['stnk_number'])) {
            \App\Models\VehicleDocument::create([
                'vehicle_id' => $vehicle->id,
                'document_type' => 'stnk',
                'document_number' => $validated['stnk_number'] ?? null,
                'file_path' => $validated['stnk_file'] ?? null,
                'expiry_date' => $validated['tax_annual_date'] ?? null,
                'notes' => 'Dokumen STNK diunggah saat registrasi unit',
            ]);
        }

        // Synchronize Pajak Tahunan if provided
        if (!empty($validated['tax_annual_date'])) {
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'pajak_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? null,
                    'expiry_date' => $validated['tax_annual_date'],
                    'notes' => 'Pajak Tahunan (PKB) dicatat saat registrasi unit',
                ]
            );
        }

        // Synchronize Pajak Plat 5 Tahunan if provided
        if (!empty($validated['tax_5year_date'])) {
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'plat_5_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? null,
                    'expiry_date' => $validated['tax_5year_date'],
                    'notes' => 'Pajak Plat 5 Tahunan dicatat saat registrasi unit',
                ]
            );
        }

        // Synchronize BPKB document
        if (!empty($validated['bpkb_file']) || !empty($validated['bpkb_number'])) {
            \App\Models\VehicleDocument::create([
                'vehicle_id' => $vehicle->id,
                'document_type' => 'bpkb',
                'document_number' => $validated['bpkb_number'] ?? null,
                'file_path' => $validated['bpkb_file'] ?? null,
                'notes' => 'Dokumen BPKB diunggah saat registrasi unit',
            ]);
        }

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan dan dokumen berhasil ditambahkan!');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load([
            'vendor',
            'location',
            'documents',
            'maintenanceRecords' => function($q) { $q->orderBy('date', 'desc'); },
            'insuranceClaims' => function($q) { $q->orderBy('incident_date', 'desc'); },
            'expenses' => function($q) { $q->orderBy('transaction_date', 'desc'); }
        ]);

        $totalMaintenanceCost = $vehicle->maintenanceRecords->sum('cost');
        $totalExpense = $vehicle->expenses->sum('amount');

        return view('vehicles.show', compact('vehicle', 'totalMaintenanceCost', 'totalExpense'));
    }

    public function edit(Vehicle $vehicle)
    {
        $locations = Location::all();
        $vendors = Vendor::all();
        $replacementVehicles = Vehicle::where('status', 'replaced')
            ->where('id', '!=', $vehicle->id)
            ->orderBy('plate_number')
            ->get();
        return view('vehicles.edit', compact('vehicle', 'locations', 'vendors', 'replacementVehicles'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'category' => 'required|string|max:100',
            'brand' => 'required|string|max:100',
            'vehicle_type' => 'nullable|string',
            'model_name' => 'required|string|max:150',
            'type_name' => 'nullable|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
            'user_holder' => 'nullable|string',
            'driver_phone' => 'nullable|string|max:50',
            'frame_number' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'year' => 'required|numeric|min:2000|max:2035',
            'color' => 'nullable|string',
            'tax_annual_date' => 'nullable|date',
            'tax_5year_date' => 'nullable|date',
            'rental_price_per_month' => 'required|numeric',
            'status' => 'required|in:active,maintenance,replaced,lost,claimed',
            'replacement_plate_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'stnk_number' => 'nullable|string|max:100',
            'bpkb_number' => 'nullable|string|max:100',
            'stnk_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'bpkb_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        if (empty($validated['vehicle_type'])) {
            $validated['vehicle_type'] = (stripos($validated['model_name'], 'supra') !== false) ? 'honda_supra_x_125' : 'honda_crf_150';
        }

        if ($request->hasFile('stnk_file')) {
            $validated['stnk_file'] = $request->file('stnk_file')->store('vehicles/documents', 'public');
            
            // Sync with VehicleDocument
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'stnk'],
                [
                    'document_number' => $validated['stnk_number'] ?? $vehicle->stnk_number,
                    'file_path' => $validated['stnk_file'],
                    'expiry_date' => $validated['tax_annual_date'] ?? $vehicle->tax_annual_date,
                    'notes' => 'Diperbarui via formulir edit kendaraan'
                ]
            );
        } elseif ($request->filled('stnk_number') && $request->stnk_number !== $vehicle->stnk_number) {
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'stnk'],
                ['document_number' => $request->stnk_number]
            );
        }

        // Sync Pajak Tahunan
        if (!empty($validated['tax_annual_date'])) {
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'pajak_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? $vehicle->stnk_number,
                    'expiry_date' => $validated['tax_annual_date'],
                    'notes' => 'Pajak Tahunan (PKB) diperbarui via data unit'
                ]
            );
        }

        // Sync Pajak Plat 5 Tahunan
        if (!empty($validated['tax_5year_date'])) {
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'plat_5_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? $vehicle->stnk_number,
                    'expiry_date' => $validated['tax_5year_date'],
                    'notes' => 'Pajak Plat 5 Tahunan diperbarui via data unit'
                ]
            );
        }

        if ($request->hasFile('bpkb_file')) {
            $validated['bpkb_file'] = $request->file('bpkb_file')->store('vehicles/documents', 'public');

            // Sync with VehicleDocument
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'bpkb'],
                [
                    'document_number' => $validated['bpkb_number'] ?? $vehicle->bpkb_number,
                    'file_path' => $validated['bpkb_file'],
                    'notes' => 'Diperbarui via formulir edit kendaraan'
                ]
            );
        } elseif ($request->filled('bpkb_number') && $request->bpkb_number !== $vehicle->bpkb_number) {
            \App\Models\VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'bpkb'],
                ['document_number' => $request->bpkb_number]
            );
        }

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Data kendaraan dan dokumen berhasil diperbarui!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dihapus!');
    }
}
