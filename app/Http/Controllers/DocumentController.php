<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleDocument;
use App\Models\Vehicle;
use App\Models\Expense;
use Carbon\Carbon;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleDocument::with(['vehicle.location', 'vehicle.vendor']);
        $activeVendorId = session('active_vendor_id');

        if ($activeVendorId) {
            $query->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }

        if ($request->filled('type')) {
            $query->where('document_type', $request->type);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filter === 'expiring_soon') {
            $query->where('expiry_date', '<=', Carbon::now()->addDays(30))
                  ->where('expiry_date', '>=', Carbon::now());
        } elseif ($request->filter === 'expired') {
            $query->where('expiry_date', '<', Carbon::now());
        }

        $perPage = in_array((int) $request->get('per_page', 15), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 15) : 15;
        $documents = $query->orderBy('expiry_date', 'asc')->paginate($perPage)->withQueryString();

        $vehicleQuery = Vehicle::orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();

        $expiringQ = VehicleDocument::where('expiry_date', '<=', Carbon::now()->addDays(30));
        if ($activeVendorId) {
            $expiringQ->whereHas('vehicle', function($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }
        $expiringCount = $expiringQ->count();

        return view('documents.index', compact('documents', 'vehicles', 'expiringCount', 'perPage'));
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
        return view('documents.create', compact('vehicles', 'selectedVehicleId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'document_type' => 'required|in:stnk,pajak_tahunan,plat_5_tahunan,bpkb,asuransi,blokir_polda,lainnya',
            'document_number' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('documents', 'public');
        }

        $doc = VehicleDocument::create($validated);

        // Record expense if cost > 0
        if ($doc->cost > 0) {
            $vehicle = Vehicle::find($doc->vehicle_id);
            $cat = in_array($doc->document_type, ['stnk', 'pajak_tahunan', 'plat_5_tahunan']) ? 'pajak_stnk' : ($doc->document_type === 'asuransi' ? 'asuransi' : ($doc->document_type === 'blokir_polda' ? 'legalitas_kepolisian' : 'overhead_admin'));

            Expense::create([
                'vehicle_id' => $doc->vehicle_id,
                'vehicle_document_id' => $doc->id,
                'expense_number' => 'EXP-DOC-' . date('Ymd') . '-' . str_pad($doc->id, 4, '0', STR_PAD_LEFT),
                'transaction_date' => $doc->issue_date ?: date('Y-m-d'),
                'category' => $cat,
                'title' => 'Biaya ' . $doc->type_label . ' - ' . ($vehicle ? $vehicle->plate_number : ''),
                'amount' => $doc->cost,
                'recipient_vendor' => 'Samsat / Kepolisian / Instansi',
                'description' => $doc->notes,
            ]);
        }

        return redirect()->route('documents.index')->with('success', 'Dokumen kendaraan berhasil disimpan!');
    }

    public function payTax(Request $request, VehicleDocument $document)
    {
        $validated = $request->validate([
            'cost' => 'required|numeric|min:0',
            'paid_date' => 'required|date',
            'next_expiry_date' => 'required|date|after:paid_date',
            'document_number' => 'nullable|string|max:100',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_file')) {
            $validated['file_path'] = $request->file('receipt_file')->store('documents', 'public');
        }

        // Update Document Record
        $document->update([
            'document_number' => $validated['document_number'] ?? $document->document_number,
            'issue_date' => $validated['paid_date'],
            'expiry_date' => $validated['next_expiry_date'],
            'cost' => $validated['cost'],
            'file_path' => $validated['file_path'] ?? $document->file_path,
            'notes' => $validated['notes'] ? ($document->notes ? $document->notes . "\n" . $validated['notes'] : $validated['notes']) : $document->notes,
        ]);

        // Update vehicle tax date if applicable
        $vehicle = $document->vehicle;
        if ($vehicle) {
            if ($document->document_type === 'pajak_tahunan' || $document->document_type === 'stnk') {
                $vehicle->update(['tax_annual_date' => $validated['next_expiry_date']]);
            } elseif ($document->document_type === 'plat_5_tahunan') {
                $vehicle->update(['tax_5year_date' => $validated['next_expiry_date']]);
            }
        }

        // Create Expense Entry in Cashflow
        if ($validated['cost'] > 0) {
            $cat = in_array($document->document_type, ['stnk', 'pajak_tahunan', 'plat_5_tahunan']) 
                ? 'pajak_stnk' 
                : ($document->document_type === 'asuransi' ? 'asuransi' : 'overhead_admin');

            Expense::create([
                'vehicle_id' => $document->vehicle_id,
                'vehicle_document_id' => $document->id,
                'expense_number' => 'EXP-TAX-' . date('Ymd') . '-' . str_pad($document->id, 4, '0', STR_PAD_LEFT),
                'transaction_date' => $validated['paid_date'],
                'category' => $cat,
                'title' => 'Pembayaran ' . $document->type_label . ' (' . ($vehicle ? $vehicle->plate_number : '') . ')',
                'amount' => $validated['cost'],
                'recipient_vendor' => 'Samsat / Bapenda',
                'description' => 'Pelunasan/Perpanjangan Pajak s/d ' . Carbon::parse($validated['next_expiry_date'])->format('d/m/Y') . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
            ]);
        }

        return redirect()->route('documents.index')->with('success', 'Pembayaran pajak berhasil dicatat dan disinkronkan ke buku kas!');
    }

    public function destroy(VehicleDocument $document)
    {
        Expense::where('vehicle_document_id', $document->id)->delete();
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus!');
    }
}
