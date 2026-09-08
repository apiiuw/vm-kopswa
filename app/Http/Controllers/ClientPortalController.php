<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vehicle;
use App\Models\ClientRequest;
use App\Models\MaintenanceRecord;
use App\Models\InsuranceClaim;

class ClientPortalController extends Controller
{
    private function getClientVendorId()
    {
        return Auth::user()->vendor_id;
    }

    public function dashboard()
    {
        $vendorId = $this->getClientVendorId();
        
        $vehiclesQuery = Vehicle::query();
        if ($vendorId) {
            $vehiclesQuery->where('vendor_id', $vendorId);
        }
        
        $totalVehicles = (clone $vehiclesQuery)->count();
        $activeVehicles = (clone $vehiclesQuery)->where('status', 'active')->count();
        $maintenanceVehicles = (clone $vehiclesQuery)->where('status', 'maintenance')->count();
        $replacedVehicles = (clone $vehiclesQuery)->where('status', 'replaced')->count();
        $lostVehicles = (clone $vehiclesQuery)->where('status', 'lost')->count();

        $requestsQuery = ClientRequest::with(['vehicle'])->where('user_id', Auth::id());
        $totalRequests = (clone $requestsQuery)->count();
        $pendingRequests = (clone $requestsQuery)->where('status', 'menunggu_review')->count();
        $processingRequests = (clone $requestsQuery)->whereIn('status', ['diproses', 'unit_dikirim'])->count();
        $completedRequests = (clone $requestsQuery)->where('status', 'selesai')->count();

        $recentRequests = (clone $requestsQuery)->latest()->take(5)->get();
        $vehiclesUnderRepair = (clone $vehiclesQuery)->whereIn('status', ['maintenance', 'replaced'])->with('location')->get();

        return view('client.dashboard', compact(
            'totalVehicles',
            'activeVehicles',
            'maintenanceVehicles',
            'replacedVehicles',
            'lostVehicles',
            'totalRequests',
            'pendingRequests',
            'processingRequests',
            'completedRequests',
            'recentRequests',
            'vehiclesUnderRepair'
        ));
    }

    public function indexRequests(Request $request)
    {
        $perPage = in_array((int) $request->get('per_page', 10), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 10) : 10;
        
        $query = ClientRequest::with(['vehicle.location'])->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->get('request_type'));
        }

        $requests = $query->latest()->paginate($perPage)->withQueryString();

        return view('client.requests.index', compact('requests', 'perPage'));
    }

    public function createRequest(Request $request)
    {
        $vendorId = $this->getClientVendorId();
        $vehiclesQuery = Vehicle::orderBy('plate_number');
        if ($vendorId) {
            $vehiclesQuery->where('vendor_id', $vendorId);
        }
        $vehicles = $vehiclesQuery->get();
        $preselectedVehicleId = $request->get('vehicle_id');
        $preselectedType = $request->get('type', 'perbaikan_servis');

        return view('client.requests.create', compact('vehicles', 'preselectedVehicleId', 'preselectedType'));
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'request_type' => 'required|in:perbaikan_servis,ganti_ban_accu,unit_pengganti,lapor_kehilangan,input_hasil_perbaikan,lainnya',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'odometer_km' => 'nullable|integer',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'workshop_name' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'proof_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('proof_files')) {
            foreach ($request->file('proof_files') as $file) {
                $path = $file->store('client_proofs', 'public');
                $uploadedFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $clientRequest = ClientRequest::create([
            'user_id' => Auth::id(),
            'vendor_id' => Auth::user()->vendor_id,
            'vehicle_id' => $validated['vehicle_id'],
            'request_type' => $validated['request_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'odometer_km' => $validated['odometer_km'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_phone' => $validated['driver_phone'] ?? null,
            'workshop_name' => $validated['workshop_name'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? 0,
            'proof_files' => $uploadedFiles,
            'status' => 'menunggu_review',
        ]);

        // Automatically update vehicle status if request is repair or lost
        if (in_array($validated['request_type'], ['perbaikan_servis', 'ganti_ban_accu'])) {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'maintenance']);
        } elseif ($validated['request_type'] === 'lapor_kehilangan') {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'lost']);
        } elseif ($validated['request_type'] === 'unit_pengganti') {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'replaced']);
        }

        return redirect()->route('client.requests.show', $clientRequest)->with('success', 'Pengajuan berhasil dikirim dan tersinkronisasi ke sistem monitoring PT SUP!');
    }

    public function showRequest(ClientRequest $clientRequest)
    {
        // Ensure client only sees their own requests
        if ($clientRequest->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $clientRequest->load(['vehicle.location', 'vendor', 'user']);
        return view('client.requests.show', compact('clientRequest'));
    }

    public function vehiclesIndex(Request $request)
    {
        $vendorId = $this->getClientVendorId();
        $perPage = in_array((int) $request->get('per_page', 10), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 10) : 10;
        
        $query = Vehicle::with(['location', 'vendor']);
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('model_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->paginate($perPage)->withQueryString();

        return view('client.vehicles.index', compact('vehicles', 'perPage'));
    }

    public function realtimeStatusJson()
    {
        $requests = ClientRequest::where('user_id', Auth::id())
            ->with(['vehicle'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'data' => $requests,
        ]);
    }
}
