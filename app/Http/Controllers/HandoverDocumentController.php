<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\HandoverDocument;
use App\Models\Vehicle;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HandoverDocumentController extends Controller
{
    public function index(Request $request)
    {
        $activeVendorId = session('active_vendor_id');
        $query = HandoverDocument::with(['vendor.client', 'vehicle.vendor.client', 'replacementVehicle'])->latest('handover_date');

        if ($activeVendorId) {
            $query->where('vendor_id', $activeVendorId);
        }

        if ($request->filled('bast_type')) {
            $query->where('bast_type', $request->bast_type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bast_number', 'like', "%{$s}%")
                    ->orWhere('received_by', 'like', "%{$s}%")
                    ->orWhereHas('vehicle', fn ($vq) => $vq->where('plate_number', 'like', "%{$s}%"));
            });
        }

        $basts = $query->paginate(15)->withQueryString();
        $vendors = Vendor::orderBy('name')->get();
        $vehicles = Vehicle::with('vendor')
            ->when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))
            ->orderBy('plate_number')
            ->get();

        $stats = [
            'total' => HandoverDocument::when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))->count(),
            'unit_baru' => HandoverDocument::when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))->where('bast_type', 'unit_baru')->count(),
            'unit_pengganti' => HandoverDocument::when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))->where('bast_type', 'unit_pengganti')->count(),
            'pengembalian' => HandoverDocument::when($activeVendorId, fn ($q) => $q->where('vendor_id', $activeVendorId))->where('bast_type', 'pengembalian')->count(),
        ];

        return view('bast.index', compact('basts', 'vendors', 'vehicles', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bast_type' => 'required|in:unit_baru,unit_pengganti,pengembalian',
            'vendor_id' => 'nullable|exists:vendors,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'replacement_vehicle_id' => 'nullable|exists:vehicles,id',
            'handover_date' => 'required|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'handed_over_by' => 'nullable|string|max:150',
            'received_by' => 'required|string|max:150',
            'recipient_position' => 'nullable|string|max:150',
            'odometer_km' => 'nullable|integer|min:0',
            'replacement_cost' => 'nullable|numeric|min:0',
            'condition_notes' => 'nullable|string',
            'signed_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $vendorId = $validated['vendor_id'] ?? $vehicle->vendor_id ?? session('active_vendor_id');
        $vendor = $vendorId ? Vendor::find($vendorId) : null;

        if ($vendor) {
            $validated['contract_start_date'] = $validated['contract_start_date'] ?? $vendor->contract_start_date?->toDateString();
            $validated['contract_end_date'] = $validated['contract_end_date'] ?? $vendor->contract_end_date?->toDateString();
        }

        if ($request->hasFile('signed_file')) {
            $validated['signed_file'] = $request->file('signed_file')->store('bast', 'public');
        }

        $prefix = match ($validated['bast_type']) {
            'unit_pengganti' => 'BAST-PG',
            'pengembalian' => 'BAST-RET',
            default => 'BAST-NEW',
        };
        $validated['bast_number'] = $prefix.'/KOPSWA/'.date('Ym').'/'.str_pad((string) (HandoverDocument::count() + 1), 4, '0', STR_PAD_LEFT);
        $validated['vendor_id'] = $vendorId;
        $validated['replacement_cost'] = $validated['replacement_cost'] ?? 0;

        $bast = HandoverDocument::create($validated);

        // Update vehicle status based on BAST type
        if ($bast->bast_type === 'unit_baru') {
            $vehicle->update(['status' => 'active', 'vendor_id' => $vendorId]);
        } elseif ($bast->bast_type === 'unit_pengganti') {
            $vehicle->update(['status' => 'replaced']);
            if ($bast->replacement_vehicle_id) {
                Vehicle::where('id', $bast->replacement_vehicle_id)->update(['status' => 'active']);
            }
            // Record replacement cost into Expense ledger if > 0
            if ((float) $bast->replacement_cost > 0) {
                Expense::create([
                    'vehicle_id' => $bast->vehicle_id,
                    'vendor_id' => $vendorId,
                    'expense_number' => 'EXP-PG-'.date('Ymd').'-'.str_pad((string) $bast->id, 4, '0', STR_PAD_LEFT),
                    'memo_number' => $bast->bast_number,
                    'transaction_date' => $bast->handover_date,
                    'category' => 'motor_pengganti',
                    'title' => 'Biaya Unit Pengganti ('.$bast->bast_number.') - '.$vehicle->plate_number,
                    'amount' => $bast->replacement_cost,
                    'recipient_vendor' => 'Operasional Unit Pengganti',
                    'description' => $bast->condition_notes ?: 'Biaya kendaraan pengganti tercatat dari BAST '.$bast->bast_number,
                ]);
            }
        } elseif ($bast->bast_type === 'pengembalian') {
            $vehicle->update(['status' => 'standby']);
        }

        return redirect()->route('bast.index')
            ->with('success', 'Dokumen '.$bast->bast_number.' berhasil diterbitkan!')
            ->with('preview_bast_id', $bast->id);
    }

    public function exportPdf(HandoverDocument $bast)
    {
        $bast->load(['vendor.client', 'vehicle.vendor.client', 'vehicle.location', 'replacementVehicle']);

        $pdf = Pdf::loadView('exports.pdf_bast', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->download(str_replace('/', '-', $bast->bast_number).'.pdf');
    }

    public function previewPdf(HandoverDocument $bast)
    {
        $bast->load(['vendor.client', 'vehicle.vendor.client', 'vehicle.location', 'replacementVehicle']);

        $pdf = Pdf::loadView('exports.pdf_bast', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->stream(str_replace('/', '-', $bast->bast_number).'.pdf');
    }

    public function uploadSigned(Request $request, HandoverDocument $bast)
    {
        $request->validate([
            'signed_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($bast->signed_file) {
            Storage::disk('public')->delete($bast->signed_file);
        }

        $path = $request->file('signed_file')->store('bast', 'public');
        $bast->update(['signed_file' => $path]);

        return redirect()->route('bast.index')->with('success', 'File BAST bertanda tangan basah ('.$bast->bast_number.') berhasil diunggah!');
    }

    public function destroy(HandoverDocument $bast)
    {
        $bast->delete();

        return redirect()->route('bast.index')->with('success', 'Dokumen BAST berhasil dihapus.');
    }
}
