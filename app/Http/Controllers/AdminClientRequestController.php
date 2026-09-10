<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientRequest;
use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\InsuranceClaim;
use App\Models\Expense;

class AdminClientRequestController extends Controller
{
    public function index(Request $request)
    {
        $activeVendorId = session('active_vendor_id');
        $perPage = in_array((int) $request->get('per_page', 10), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 10) : 10;
        
        $query = ClientRequest::with(['vehicle.location', 'vendor', 'user'])->latest();

        if ($activeVendorId) {
            $query->where('vendor_id', $activeVendorId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->get('request_type'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($vq) use ($search) {
                      $vq->where('plate_number', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->paginate($perPage)->withQueryString();

        $statsQuery = ClientRequest::query();
        if ($activeVendorId) {
            $statsQuery->where('vendor_id', $activeVendorId);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'menunggu_review')->count(),
            'processing' => (clone $statsQuery)->whereIn('status', ['diproses', 'unit_dikirim'])->count(),
            'completed' => (clone $statsQuery)->where('status', 'selesai')->count(),
        ];

        return view('admin_client_requests.index', compact('requests', 'stats', 'perPage'));
    }

    public function show(ClientRequest $clientRequest)
    {
        $clientRequest->load(['vehicle.location', 'vendor', 'user']);
        return view('admin_client_requests.show', compact('clientRequest'));
    }

    public function updateStatus(Request $request, ClientRequest $clientRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu_review,diproses,unit_dikirim,selesai,ditolak',
            'admin_response' => 'nullable|string',
            'sync_to_maintenance' => 'nullable|boolean',
            'sync_to_insurance' => 'nullable|boolean',
        ]);

        $clientRequest->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'],
            'resolved_at' => $validated['status'] === 'selesai' ? now() : null,
        ]);

        // Auto sync to Maintenance Record if requested and completed
        if ($request->boolean('sync_to_maintenance') && $validated['status'] === 'selesai') {
            $record = MaintenanceRecord::create([
                'vehicle_id' => $clientRequest->vehicle_id,
                'type' => match($clientRequest->request_type) {
                    'ganti_ban_accu' => 'ganti_ban',
                    'unit_pengganti' => 'motor_pengganti',
                    default => 'service_berkala',
                },
                'date' => now(),
                'odometer_km' => $clientRequest->odometer_km,
                'workshop_name' => $clientRequest->workshop_name ?: 'Bengkel Rekanan',
                'description' => $clientRequest->title . ' - ' . $clientRequest->description,
                'cost' => $clientRequest->estimated_cost ?: 0,
                'memo_number' => 'REQ-KLIEN-' . str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT),
            ]);

            // Sync to Expense ledger
            $vehicle = Vehicle::find($clientRequest->vehicle_id);
            Expense::create([
                'vehicle_id' => $record->vehicle_id,
                'vendor_id' => $vehicle ? $vehicle->vendor_id : null,
                'maintenance_record_id' => $record->id,
                'expense_number' => 'EXP-' . date('Ymd') . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                'memo_number' => $record->memo_number,
                'transaction_date' => $record->date,
                'category' => match($clientRequest->request_type) {
                    'ganti_ban_accu' => 'ban',
                    'unit_pengganti' => 'motor_pengganti',
                    default => 'service_berkala',
                },
                'title' => ($clientRequest->title ?: 'Pemeliharaan Unit') . ' - ' . ($vehicle ? $vehicle->plate_number : ''),
                'amount' => $record->cost,
                'recipient_vendor' => $record->workshop_name ?: 'Bengkel Rekanan',
                'description' => $record->description,
            ]);

            // Restore vehicle to active
            Vehicle::where('id', $clientRequest->vehicle_id)->update(['status' => 'active']);
        }

        // Auto sync to Insurance Claim if lost
        if ($request->boolean('sync_to_insurance') && $clientRequest->request_type === 'lapor_kehilangan') {
            InsuranceClaim::create([
                'vehicle_id' => $clientRequest->vehicle_id,
                'claim_type' => 'tlo_kehilangan',
                'incident_date' => $clientRequest->created_at->toDateString(),
                'claim_amount' => $clientRequest->estimated_cost ?: 37800000,
                'status' => 'diajukan',
                'incident_chronology' => $clientRequest->description,
                'driver_name' => $clientRequest->driver_name,
            ]);

            Vehicle::where('id', $clientRequest->vehicle_id)->update(['status' => 'lost']);
        }

        return redirect()->route('admin.client-requests.show', $clientRequest)->with('success', 'Status pengajuan berhasil diperbarui dan disinkronkan ke portal klien!');
    }
}
