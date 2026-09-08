<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\Expense;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['vehicle.location', 'vehicle.vendor']);
        $activeVendorId = session('active_vendor_id');

        if ($activeVendorId) {
            $query->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('memo_number', 'like', "%{$s}%")
                  ->orWhere('item_name', 'like', "%{$s}%")
                  ->orWhereHas('vehicle', function($vq) use ($s) {
                      $vq->where('plate_number', 'like', "%{$s}%");
                  });
            });
        }

        $perPage = in_array((int) $request->get('per_page', 15), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 15) : 15;
        $records = $query->orderBy('date', 'desc')->paginate($perPage)->withQueryString();

        $vehicleQuery = Vehicle::orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();

        $statsQuery = MaintenanceRecord::query();
        if ($activeVendorId) {
            $statsQuery->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }

        $stats = [
            'total_cost' => (clone $statsQuery)->sum('cost'),
            'service_cost' => (clone $statsQuery)->where('type', 'service_berkala')->sum('cost'),
            'ban_cost' => (clone $statsQuery)->where('type', 'ganti_ban')->sum('cost'),
            'accu_cost' => (clone $statsQuery)->where('type', 'ganti_accu')->sum('cost'),
            'sparepart_cost' => (clone $statsQuery)->where('type', 'sparepart')->sum('cost'),
            'motor_pengganti_cost' => (clone $statsQuery)->where('type', 'motor_pengganti')->sum('cost'),
        ];

        return view('maintenance.index', compact('records', 'vehicles', 'stats', 'perPage'));
    }

    public function create(Request $request)
    {
        $activeVendorId = session('active_vendor_id');
        $vehicleQuery = Vehicle::orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();
        $selectedVehicleId = $request->vehicle_id;
        $defaultType = $request->type ?? 'service_berkala';

        return view('maintenance.create', compact('vehicles', 'selectedVehicleId', 'defaultType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:service_berkala,ganti_ban,ganti_accu,sparepart,motor_pengganti,lainnya',
            'date' => 'required|date',
            'odometer_km' => 'nullable|numeric',
            'workshop_name' => 'nullable|string',
            'item_name' => 'nullable|string',
            'qty' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'memo_number' => 'nullable|string',
            'description' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        $record = MaintenanceRecord::create($validated);

        // Map type to expense category
        $categoryMap = [
            'service_berkala' => 'service_berkala',
            'ganti_ban' => 'ban',
            'ganti_accu' => 'accu',
            'sparepart' => 'sparepart',
            'motor_pengganti' => 'motor_pengganti',
            'lainnya' => 'overhead_admin',
        ];

        $vehicle = Vehicle::find($validated['vehicle_id']);

        // Automatically sync to Expense ledger
        Expense::create([
            'vehicle_id' => $record->vehicle_id,
            'maintenance_record_id' => $record->id,
            'expense_number' => 'EXP-' . date('Ymd') . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
            'memo_number' => $record->memo_number,
            'transaction_date' => $record->date,
            'category' => $categoryMap[$record->type] ?? 'service_berkala',
            'title' => ($record->item_name ?: $record->type_label) . ' - ' . ($vehicle ? $vehicle->plate_number : ''),
            'amount' => $record->cost,
            'recipient_vendor' => $record->workshop_name ?: 'Bengkel / Vendor Rekanan',
            'description' => $record->description,
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Catatan pemeliharaan & pengeluaran berhasil disimpan!');
    }

    public function destroy(MaintenanceRecord $maintenance)
    {
        // Delete attached expense if exists
        Expense::where('maintenance_record_id', $maintenance->id)->delete();
        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Catatan pemeliharaan berhasil dihapus!');
    }
}
