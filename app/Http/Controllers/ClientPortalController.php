<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $vehiclesQuery = Vehicle::with(['location'])
            ->where('status', '!=', 'lost')
            ->orderBy('plate_number');

        if ($vendorId) {
            $vehiclesQuery->where('vendor_id', $vendorId);
        }
        $vehicles = $vehiclesQuery->get();
        $preselectedVehicleId = $request->get('vehicle_id');
        $preselectedType = $request->get('type', 'perbaikan_servis');
        $clientVendor = Auth::user()->vendor;

        return view('client.requests.create', compact('vehicles', 'preselectedVehicleId', 'preselectedType', 'clientVendor'));
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'request_type' => 'required|in:perbaikan_servis,unit_pengganti,lapor_kehilangan',
            'repair_category' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'odometer_km' => 'nullable|integer',
            'vehicle_operational_status' => 'nullable|string',
            'vehicle_current_location' => 'nullable|string',
            'vehicle_condition_notes' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'pic_name' => 'nullable|string',
            'pic_phone' => 'nullable|string',
            'workshop_name' => 'nullable|string',
            'workshop_phone' => 'nullable|string',
            'mechanic_name' => 'nullable|string',
            'mechanic_phone' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'photo_front' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photo_right' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photo_left' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photo_behind' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'proof_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->status === 'lost') {
            return back()->withInput()->with('error', 'Unit kendaraan ini berstatus hilang (TLO) dan tidak dapat diajukan servis atau permohonan baru.');
        }

        $uploadedFiles = [];

        // Extract 4 standard vehicle photos
        $vehicleAngles = [
            'photo_front' => 'Foto Tampak Depan (Nopol Terlihat)',
            'photo_right' => 'Foto Tampak Samping Kanan',
            'photo_left' => 'Foto Tampak Samping Kiri',
            'photo_behind' => 'Foto Tampak Belakang (Nopol Terlihat)',
        ];

        foreach ($vehicleAngles as $fieldKey => $label) {
            if ($request->hasFile($fieldKey)) {
                $file = $request->file($fieldKey);
                if ($file && $file->isValid()) {
                    $path = $file->store('client_proofs', 'public');
                    $uploadedFiles[] = [
                        'path' => $path,
                        'name' => "{$label} - ".$file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ];
                }
            }
        }

        // Extract global proof files
        if ($request->hasFile('proof_files')) {
            foreach ($request->file('proof_files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('client_proofs', 'public');
                    $uploadedFiles[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ];
                }
            }
        }

        // Extract individual complaint photos (complaint_photos_0, complaint_photos_1, etc.)
        foreach ($request->allFiles() as $key => $files) {
            if (str_starts_with($key, 'complaint_photos_')) {
                $complaintNum = str_replace('complaint_photos_', '', $key);
                $complaintIdx = is_numeric($complaintNum) ? ((int) $complaintNum + 1) : $complaintNum;
                $fileList = is_array($files) ? $files : [$files];
                foreach ($fileList as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('client_proofs', 'public');
                        $uploadedFiles[] = [
                            'path' => $path,
                            'name' => "Foto Keluhan #{$complaintIdx} - ".$file->getClientOriginalName(),
                            'complaint_index' => (int) $complaintNum,
                            'size' => $file->getSize(),
                        ];
                    }
                }
            }
        }

        // Format title or description if specific category selected
        $title = $validated['title'];
        $description = $validated['description'];

        // Append Workshop and Mechanic details if provided
        $workshopDetails = [];
        if (! empty($validated['workshop_phone'])) {
            $workshopDetails[] = "Telp Bengkel: {$validated['workshop_phone']}";
        }
        if (! empty($validated['mechanic_name'])) {
            $workshopDetails[] = "Mekanik: {$validated['mechanic_name']}".(! empty($validated['mechanic_phone']) ? " (Telp: {$validated['mechanic_phone']})" : '');
        }
        if (! empty($workshopDetails)) {
            $description = '['.implode(' | ', $workshopDetails)."]\n".$description;
        }

        // Append Vehicle Technical Status Info if provided
        $statusLabels = [
            'masih_bisa_jalan' => 'Masih Bisa Jalan Normal / Pelan',
            'mogok_total' => 'Mogok Total / Tidak Bisa Dihidupkan',
            'rusak_tidak_aman' => 'Bisa Hidup Tapi Berbahaya / Tidak Aman',
            'sudah_di_bengkel' => 'Unit Sudah Masuk / Menginap di Bengkel',
            'unit_hilang' => 'Unit Hilang / Tidak di Tempat (TLO)',
        ];
        $opStatus = ! empty($validated['vehicle_operational_status']) ? ($statusLabels[$validated['vehicle_operational_status']] ?? $validated['vehicle_operational_status']) : null;

        $techInfo = [];
        if ($opStatus) {
            $techInfo[] = "Kondisi Operasional: {$opStatus}";
        }
        if (! empty($validated['vehicle_current_location'])) {
            $techInfo[] = "Posisi Unit: {$validated['vehicle_current_location']}";
        }
        if (! empty($validated['vehicle_condition_notes'])) {
            $techInfo[] = "Catatan Fisik: {$validated['vehicle_condition_notes']}";
        }
        if (! empty($techInfo)) {
            $description = '['.implode(' | ', $techInfo)."]\n".$description;
        }

        // Append PIC details to description if provided
        if (! empty($validated['pic_name'])) {
            $picInfo = "[PIC Pengaju: {$validated['pic_name']}".(! empty($validated['pic_phone']) ? " - Telp: {$validated['pic_phone']}" : '').']';
            $description = $picInfo."\n".$description;
        }

        if (! empty($validated['repair_category']) && $validated['request_type'] === 'perbaikan_servis') {
            $categoryLabels = [
                'servis_berkala' => 'Servis Berkala & Tune Up',
                'ganti_ban' => 'Penggantian Ban',
                'ganti_accu' => 'Penggantian Accu / Aki',
                'sparepart_fast_moving' => 'Penggantian Sparepart Fast Moving',
            ];
            $catLabel = $categoryLabels[$validated['repair_category']] ?? $validated['repair_category'];
            $description = "[Kategori: {$catLabel}]\n".$description;
        }

        $clientRequest = ClientRequest::create([
            'user_id' => Auth::id(),
            'vendor_id' => Auth::user()->vendor_id,
            'vehicle_id' => $validated['vehicle_id'],
            'request_type' => $validated['request_type'],
            'title' => $title,
            'description' => $description,
            'odometer_km' => $validated['odometer_km'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_phone' => $validated['driver_phone'] ?? null,
            'workshop_name' => $validated['workshop_name'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? 0,
            'proof_files' => $uploadedFiles,
            'status' => 'menunggu_review',
        ]);

        // Automatically update vehicle status based on request type
        if ($validated['request_type'] === 'perbaikan_servis') {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'maintenance']);
        } elseif ($validated['request_type'] === 'lapor_kehilangan') {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'lost']);
        } elseif ($validated['request_type'] === 'unit_pengganti') {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'replaced']);
        }

        return redirect()->route('client.requests.show', $clientRequest)->with('success', 'Pengajuan berhasil dikirim dan tersinkronisasi ke sistem monitoring Koperasi Swadharma!');
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
            $query->where(function ($q) use ($search) {
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
