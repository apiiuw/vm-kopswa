<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Income;
use App\Models\Location;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\Vendor;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            $query->where(function ($q) use ($s) {
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

        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
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
            'standby' => (clone $statsQuery)->where('status', 'standby')->count(),
            'maintenance' => (clone $statsQuery)->where('status', 'maintenance')->count(),
            'replaced' => (clone $statsQuery)->where('status', 'replaced')->count(),
            'sold' => (clone $statsQuery)->where('status', 'sold')->count(),
            'lost' => (clone $statsQuery)->where('status', 'lost')->count(),
            'total_rental' => (clone $statsQuery)->sum('rental_price_per_month'),
            'mobil' => (clone $statsQuery)->where('asset_type', 'mobil')->count(),
            'motor' => (clone $statsQuery)->where('asset_type', 'motor')->count(),
            'fotocopy' => (clone $statsQuery)->where('asset_type', 'fotocopy')->count(),
            'dispenser' => (clone $statsQuery)->where('asset_type', 'dispenser')->count(),
        ];

        return view('vehicles.index', compact('vehicles', 'locations', 'vendors', 'categories', 'brands', 'currentVendor', 'activeVendorId', 'perPage', 'stats'));
    }

    public function create()
    {
        $locations = Location::all();
        $clients = Client::orderBy('name')->get();
        $vendors = Vendor::with('client')->orderBy('name')->get();
        $replacementVehicles = Vehicle::where('status', 'replaced')->orderBy('plate_number')->get();
        $nextAssetCodes = [
            'motor' => Vehicle::generateNextAssetCode('motor'),
            'mobil' => Vehicle::generateNextAssetCode('mobil'),
            'fotocopy' => Vehicle::generateNextAssetCode('fotocopy'),
            'dispenser' => Vehicle::generateNextAssetCode('dispenser'),
        ];

        return view('vehicles.create', compact('locations', 'clients', 'vendors', 'replacementVehicles', 'nextAssetCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'asset_type' => 'nullable|in:mobil,motor,fotocopy,dispenser',
            'asset_code' => 'nullable|string|max:80',
            'serial_number' => 'nullable|string|max:120',
            'plate_number' => 'nullable|string|unique:vehicles,plate_number',
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
            'total_contract_value' => 'nullable|numeric|min:0',
            'additional_contract_value' => 'nullable|numeric|min:0',
            'additional_monthly_value' => 'nullable|numeric|min:0',
            'billing_cycle' => 'nullable|in:bulanan,triwulanan,semesteran,tahunan,2_tahunan',
            'invoice_value' => 'nullable|numeric|min:0',
            'acquisition_value' => 'nullable|numeric|min:0',
            'ppn_pm' => 'nullable|numeric|min:0',
            'dealer_name' => 'nullable|string|max:150',
            'dealer_discount' => 'nullable|numeric|min:0',
            'acquisition_date' => 'nullable|date',
            'useful_life_months' => 'nullable|integer|min:1|max:240',
            'residual_value' => 'nullable|numeric|min:0',
            'residual_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,standby,maintenance,replaced,sold,lost,claimed',
            'sold_date' => 'nullable|date',
            'sold_price' => 'nullable|numeric|min:0',
            'sold_to' => 'nullable|string|max:150',
            'replacement_plate_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'stnk_number' => 'nullable|string|max:100',
            'bpkb_number' => 'nullable|string|max:100',
            'stnk_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'bpkb_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        $validated['asset_type'] = $validated['asset_type'] ?? 'motor';
        if (empty($validated['asset_code'])) {
            $validated['asset_code'] = Vehicle::generateNextAssetCode($validated['asset_type']);
        }
        if (empty($validated['plate_number'])) {
            $validated['plate_number'] = $validated['asset_code'] ?: ($validated['serial_number'] ?: strtoupper($validated['asset_type']).'-'.date('YmdHis').'-'.rand(10, 99));
        }
        $validated['billing_cycle'] = $validated['billing_cycle'] ?? 'bulanan';
        $validated['total_contract_value'] = $validated['total_contract_value'] ?? 0;
        $validated['additional_contract_value'] = $validated['additional_contract_value'] ?? 0;
        $validated['additional_monthly_value'] = $validated['additional_monthly_value'] ?? 0;
        $validated['invoice_value'] = $validated['invoice_value'] ?? 0;
        $validated['acquisition_value'] = $validated['acquisition_value'] ?? $validated['invoice_value'];
        $validated['ppn_pm'] = $validated['ppn_pm'] ?? max(0, (float) $validated['invoice_value'] - (float) $validated['acquisition_value']);
        $validated['dealer_discount'] = $validated['dealer_discount'] ?? 0;
        $validated['useful_life_months'] = $validated['useful_life_months'] ?? 48;
        $validated['residual_percentage'] = $validated['residual_percentage'] ?? 0;
        $validated['residual_value'] = $validated['residual_value'] ?? round((float) $validated['acquisition_value'] * ((float) $validated['residual_percentage'] / 100), 2);

        if (empty($validated['vehicle_type'])) {
            $validated['vehicle_type'] = (stripos($validated['model_name'], 'supra') !== false) ? 'honda_supra_x_125' : 'honda_crf_150';
        }

        if (empty($validated['vendor_id']) && $validated['status'] === 'active' && session('active_vendor_id')) {
            $validated['vendor_id'] = session('active_vendor_id');
        }
        if ($validated['status'] === 'standby') {
            $validated['vendor_id'] = null;
        }

        if ($request->hasFile('stnk_file')) {
            $validated['stnk_file'] = $request->file('stnk_file')->store('vehicles/documents', 'public');
        }

        if ($request->hasFile('bpkb_file')) {
            $validated['bpkb_file'] = $request->file('bpkb_file')->store('vehicles/documents', 'public');
        }

        $vehicle = Vehicle::create($validated);

        // Automatically record Dealer Discount as Pendapatan Lain-lain if > 0
        $this->recordDealerDiscountIncome($vehicle);

        // Synchronize STNK document
        if (! empty($validated['stnk_file']) || ! empty($validated['stnk_number'])) {
            VehicleDocument::create([
                'vehicle_id' => $vehicle->id,
                'document_type' => 'stnk',
                'document_number' => $validated['stnk_number'] ?? null,
                'file_path' => $validated['stnk_file'] ?? null,
                'expiry_date' => $validated['tax_annual_date'] ?? null,
                'notes' => 'Dokumen STNK diunggah saat registrasi unit',
            ]);
        }

        // Synchronize Pajak Tahunan if provided
        if (! empty($validated['tax_annual_date'])) {
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'pajak_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? null,
                    'expiry_date' => $validated['tax_annual_date'],
                    'notes' => 'Pajak Tahunan (PKB) dicatat saat registrasi unit',
                ]
            );
        }

        // Synchronize Pajak Plat 5 Tahunan if provided
        if (! empty($validated['tax_5year_date'])) {
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'plat_5_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? null,
                    'expiry_date' => $validated['tax_5year_date'],
                    'notes' => 'Pajak Plat 5 Tahunan dicatat saat registrasi unit',
                ]
            );
        }

        // Synchronize BPKB document
        if (! empty($validated['bpkb_file']) || ! empty($validated['bpkb_number'])) {
            VehicleDocument::create([
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
            'maintenanceRecords' => function ($q) {
                $q->orderBy('date', 'desc');
            },
            'insuranceClaims' => function ($q) {
                $q->orderBy('incident_date', 'desc');
            },
            'expenses' => function ($q) {
                $q->orderBy('transaction_date', 'desc');
            },
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
            'asset_type' => 'nullable|in:mobil,motor,fotocopy,dispenser',
            'asset_code' => 'nullable|string|max:80',
            'serial_number' => 'nullable|string|max:120',
            'plate_number' => 'required|string|unique:vehicles,plate_number,'.$vehicle->id,
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
            'total_contract_value' => 'nullable|numeric|min:0',
            'additional_contract_value' => 'nullable|numeric|min:0',
            'additional_monthly_value' => 'nullable|numeric|min:0',
            'billing_cycle' => 'nullable|in:bulanan,triwulanan,semesteran,tahunan,2_tahunan',
            'invoice_value' => 'nullable|numeric|min:0',
            'acquisition_value' => 'nullable|numeric|min:0',
            'ppn_pm' => 'nullable|numeric|min:0',
            'dealer_name' => 'nullable|string|max:150',
            'dealer_discount' => 'nullable|numeric|min:0',
            'acquisition_date' => 'nullable|date',
            'useful_life_months' => 'nullable|integer|min:1|max:240',
            'residual_value' => 'nullable|numeric|min:0',
            'residual_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,standby,maintenance,replaced,sold,lost,claimed',
            'sold_date' => 'nullable|date',
            'sold_price' => 'nullable|numeric|min:0',
            'sold_to' => 'nullable|string|max:150',
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
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'stnk'],
                [
                    'document_number' => $validated['stnk_number'] ?? $vehicle->stnk_number,
                    'file_path' => $validated['stnk_file'],
                    'expiry_date' => $validated['tax_annual_date'] ?? $vehicle->tax_annual_date,
                    'notes' => 'Diperbarui via formulir edit kendaraan',
                ]
            );
        } elseif ($request->filled('stnk_number') && $request->stnk_number !== $vehicle->stnk_number) {
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'stnk'],
                ['document_number' => $request->stnk_number]
            );
        }

        // Sync Pajak Tahunan
        if (! empty($validated['tax_annual_date'])) {
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'pajak_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? $vehicle->stnk_number,
                    'expiry_date' => $validated['tax_annual_date'],
                    'notes' => 'Pajak Tahunan (PKB) diperbarui via data unit',
                ]
            );
        }

        // Sync Pajak Plat 5 Tahunan
        if (! empty($validated['tax_5year_date'])) {
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'plat_5_tahunan'],
                [
                    'document_number' => $validated['stnk_number'] ?? $vehicle->stnk_number,
                    'expiry_date' => $validated['tax_5year_date'],
                    'notes' => 'Pajak Plat 5 Tahunan diperbarui via data unit',
                ]
            );
        }

        if ($request->hasFile('bpkb_file')) {
            $validated['bpkb_file'] = $request->file('bpkb_file')->store('vehicles/documents', 'public');

            // Sync with VehicleDocument
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'bpkb'],
                [
                    'document_number' => $validated['bpkb_number'] ?? $vehicle->bpkb_number,
                    'file_path' => $validated['bpkb_file'],
                    'notes' => 'Diperbarui via formulir edit kendaraan',
                ]
            );
        } elseif ($request->filled('bpkb_number') && $request->bpkb_number !== $vehicle->bpkb_number) {
            VehicleDocument::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'document_type' => 'bpkb'],
                ['document_number' => $request->bpkb_number]
            );
        }

        $vehicle->update($validated);
        $this->recordDealerDiscountIncome($vehicle->fresh());

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Data kendaraan dan dokumen berhasil diperbarui!');
    }

    private function recordDealerDiscountIncome(Vehicle $vehicle): void
    {
        if ((float) $vehicle->dealer_discount <= 0) {
            return;
        }

        $alreadyRecorded = Income::where('vehicle_id', $vehicle->id)
            ->where('income_type', 'lain_lain')
            ->exists();

        if ($alreadyRecorded) {
            return;
        }

        Income::create([
            'vendor_id' => $vehicle->vendor_id,
            'vehicle_id' => $vehicle->id,
            'income_type' => 'lain_lain',
            'invoice_number' => 'DISC-DLR-'.date('Ymd').'-'.str_pad((string) $vehicle->id, 4, '0', STR_PAD_LEFT),
            'period_month_year' => ($vehicle->acquisition_date ? $vehicle->acquisition_date->format('Y-m') : date('Y-m')),
            'invoice_date' => $vehicle->acquisition_date ?? now()->toDateString(),
            'client_name' => $vehicle->dealer_name ?: 'Dealer Pengadaan Unit',
            'source_name' => $vehicle->dealer_name ?: 'Dealer Pengadaan Unit',
            'gross_amount' => $vehicle->dealer_discount,
            'ppn_rate' => 0,
            'ppn_amount' => 0,
            'total_amount_with_ppn' => $vehicle->dealer_discount,
            'paid_amount' => $vehicle->dealer_discount,
            'status' => 'lunas',
            'payment_date' => $vehicle->acquisition_date ?? now()->toDateString(),
            'notes' => 'Diskon Dealer pengadaan unit '.$vehicle->plate_number.' ('.$vehicle->model_name.')',
        ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Aset/Kendaraan berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $vendorId = $request->input('vendor_id') ?: session('active_vendor_id');
        $spreadsheet = IOFactory::load($request->file('excel_file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $imported = 0;
        foreach ($rows as $idx => $row) {
            if ($idx === 0) {
                continue; // skip header
            }
            $plate = trim((string) ($row[0] ?? ''));
            if ($plate === '') {
                continue;
            }
            $assetType = strtolower(trim((string) ($row[1] ?? 'motor')));
            if (! in_array($assetType, ['mobil', 'motor', 'fotocopy', 'dispenser'], true)) {
                $assetType = 'motor';
            }
            $brand = trim((string) ($row[2] ?? 'Honda'));
            $model = trim((string) ($row[3] ?? 'Unit Operasional'));
            $year = is_numeric($row[4] ?? null) ? (int) $row[4] : (int) date('Y');
            $rental = is_numeric($row[5] ?? null) ? (float) $row[5] : 0;
            $acq = is_numeric($row[6] ?? null) ? (float) $row[6] : 0;
            $status = strtolower(trim((string) ($row[7] ?? 'active')));
            if (! in_array($status, ['active', 'standby', 'maintenance', 'replaced', 'sold', 'lost', 'claimed'], true)) {
                $status = 'active';
            }

            Vehicle::updateOrCreate(
                ['plate_number' => $plate],
                [
                    'vendor_id' => $vendorId,
                    'asset_type' => $assetType,
                    'category' => ucfirst($assetType),
                    'brand' => $brand,
                    'model_name' => $model,
                    'vehicle_type' => stripos($model, 'supra') !== false ? 'honda_supra_x_125' : 'honda_crf_150',
                    'year' => $year,
                    'rental_price_per_month' => $rental,
                    'invoice_value' => $acq,
                    'acquisition_value' => $acq,
                    'status' => $status,
                ]
            );
            $imported++;
        }

        return redirect()->route('vehicles.index')->with('success', "Berhasil mengimpor/memperbarui {$imported} unit aset dari file Excel/CSV!");
    }
}
