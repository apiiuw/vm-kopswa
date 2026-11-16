<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
use App\Models\Expense;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['vehicle.location', 'vehicle.vendor']);
        $activeVendorId = session('active_vendor_id');

        if ($activeVendorId) {
            $query->whereHas('vehicle', function ($q) use ($activeVendorId) {
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
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                    ->orWhere('memo_number', 'like', "%{$s}%")
                    ->orWhere('item_name', 'like', "%{$s}%")
                    ->orWhereHas('vehicle', function ($vq) use ($s) {
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
            $statsQuery->whereHas('vehicle', function ($q) use ($activeVendorId) {
                $q->where('vendor_id', $activeVendorId);
            });
        }

        $allRecords = (clone $statsQuery)->get();

        $totalCost = 0;
        $serviceCost = 0;
        $banCost = 0;
        $accuCost = 0;
        $sparepartCost = 0;
        $motorPenggantiCost = 0;

        foreach ($allRecords as $record) {
            $recordCost = (float) $record->cost;
            $totalCost += $recordCost;

            $complaints = $record->parsed_complaints;

            if (! empty($complaints) && count($complaints) > 0) {
                $hasCostOnComplaints = false;
                foreach ($complaints as $c) {
                    $itemCost = (float) str_replace(['.', ','], '', $c['cost'] ?? '0');
                    if ($itemCost > 0) {
                        $hasCostOnComplaints = true;
                        $cat = strtolower($c['category'] ?? '');
                        $title = strtolower($c['title'] ?? '');

                        if (str_contains($cat, 'aki') || str_contains($cat, 'accu') || str_contains($title, 'aki') || str_contains($title, 'accu')) {
                            $accuCost += $itemCost;
                        } elseif (str_contains($cat, 'ban') || str_contains($title, 'ban')) {
                            $banCost += $itemCost;
                        } elseif (str_contains($cat, 'service') || str_contains($cat, 'pelumas') || str_contains($cat, 'oli') || str_contains($title, 'service') || str_contains($title, 'oli')) {
                            $serviceCost += $itemCost;
                        } elseif (str_contains($cat, 'motor_pengganti') || str_contains($cat, 'pengganti') || str_contains($title, 'motor pengganti')) {
                            $motorPenggantiCost += $itemCost;
                        } else {
                            $sparepartCost += $itemCost;
                        }
                    }
                }

                // If complaints didn't have individual item costs parsed, attribute to the main record type
                if (! $hasCostOnComplaints) {
                    match ($record->type) {
                        'ganti_ban' => $banCost += $recordCost,
                        'ganti_accu' => $accuCost += $recordCost,
                        'sparepart' => $sparepartCost += $recordCost,
                        'motor_pengganti' => $motorPenggantiCost += $recordCost,
                        default => $serviceCost += $recordCost,
                    };
                }
            } else {
                match ($record->type) {
                    'ganti_ban' => $banCost += $recordCost,
                    'ganti_accu' => $accuCost += $recordCost,
                    'sparepart' => $sparepartCost += $recordCost,
                    'motor_pengganti' => $motorPenggantiCost += $recordCost,
                    default => $serviceCost += $recordCost,
                };
            }
        }

        $stats = [
            'total_cost' => $totalCost,
            'service_cost' => $serviceCost,
            'ban_cost' => $banCost,
            'accu_cost' => $accuCost,
            'sparepart_cost' => $sparepartCost,
            'motor_pengganti_cost' => $motorPenggantiCost,
        ];

        return view('maintenance.index', compact('records', 'vehicles', 'stats', 'perPage'));
    }

    public function create(Request $request)
    {
        $activeVendorId = session('active_vendor_id');
        $vehicleQuery = Vehicle::with(['location', 'maintenanceRecords' => fn ($q) => $q->orderBy('date', 'desc')])->orderBy('plate_number');
        if ($activeVendorId) {
            $vehicleQuery->where('vendor_id', $activeVendorId);
        }
        $vehicles = $vehicleQuery->get();
        $selectedVehicleId = $request->vehicle_id;
        $defaultType = $request->type ?? 'service_berkala';

        // Get Client Requests for Autofill / Reference
        $requestQuery = ClientRequest::with(['vehicle.location', 'vendor', 'user'])->latest();
        if ($activeVendorId) {
            $requestQuery->where('vendor_id', $activeVendorId);
        }
        $rawClientRequests = $requestQuery->get();

        // Parse structured metadata and complaints for each client request
        $clientRequests = $rawClientRequests->map(function ($cr) {
            $normalizedDesc = str_replace("\r\n", "\n", $cr->description ?? '');
            $descLines = explode("\n", $normalizedDesc);

            $picName = '';
            $picPhone = '';
            $opStatus = '';
            $unitPos = '';
            $physNotes = '';
            $wsPhone = '';
            $mechName = '';
            $mechPhone = '';

            $complaints = [];
            $currentComplaint = null;

            foreach ($descLines as $line) {
                $trimmed = trim($line);
                if (empty($trimmed)) {
                    continue;
                }

                if (str_starts_with($trimmed, '[PIC Pengaju:') || str_starts_with($trimmed, '[PIC:')) {
                    $content = trim($trimmed, '[]');
                    if (preg_match('/PIC(?: Pengaju)?:\s*([^-]+?)(?:\s*-\s*Telp:\s*(.*))?$/i', $content, $pm)) {
                        $picName = trim($pm[1] ?? '');
                        $picPhone = trim($pm[2] ?? '');
                    }
                } elseif (str_starts_with($trimmed, '[Kondisi Operasional:') || str_contains($trimmed, 'Kondisi Operasional:') || str_starts_with($trimmed, '[Kondisi:')) {
                    $content = trim($trimmed, '[]');
                    $parts = explode('|', $content);
                    foreach ($parts as $part) {
                        $partTrim = trim($part);
                        if (str_starts_with($partTrim, 'Kondisi Operasional:')) {
                            $opStatus = trim(substr($partTrim, 20));
                        } elseif (str_starts_with($partTrim, 'Kondisi:')) {
                            $opStatus = trim(substr($partTrim, 8));
                        } elseif (str_starts_with($partTrim, 'Posisi Unit:') || str_starts_with($partTrim, 'Lokasi:')) {
                            $unitPos = trim(substr($partTrim, strpos($partTrim, ':') + 1));
                        } elseif (str_starts_with($partTrim, 'Catatan Fisik:')) {
                            $physNotes = trim(substr($partTrim, 14));
                        }
                    }
                } elseif (str_starts_with($trimmed, '[Telp Bengkel:') || str_contains($trimmed, 'Telp Bengkel:') || str_contains($trimmed, 'Mekanik:') || str_starts_with($trimmed, '[Bengkel:')) {
                    $content = trim($trimmed, '[]');
                    $parts = explode('|', $content);
                    foreach ($parts as $part) {
                        $partTrim = trim($part);
                        if (str_starts_with($partTrim, 'Telp Bengkel:') || str_starts_with($partTrim, 'Telp:')) {
                            $wsPhone = trim(substr($partTrim, strpos($partTrim, ':') + 1));
                        } elseif (str_starts_with($partTrim, 'Mekanik:')) {
                            $mContent = trim(substr($partTrim, 8));
                            if (preg_match('/^(.*?)\s*\(Telp:\s*(.*?)\)$/i', $mContent, $mm)) {
                                $mechName = trim($mm[1]);
                                $mechPhone = trim($mm[2]);
                            } elseif (preg_match('/^(.*?)\s*\((.*?)\)$/i', $mContent, $mm)) {
                                $mechName = trim($mm[1]);
                                $mechPhone = trim($mm[2]);
                            } else {
                                $mechName = $mContent;
                            }
                        }
                    }
                } elseif (preg_match('/^\[(?:Keluhan|Item) #(\d+): (.*?)\]$/', $trimmed, $m)) {
                    if ($currentComplaint) {
                        $complaints[] = $currentComplaint;
                    }
                    $categoryRaw = strtolower($m[2]);
                    $catVal = 'service_berkala';
                    if (str_contains($categoryRaw, 'ban')) {
                        $catVal = 'ganti_ban';
                    } elseif (str_contains($categoryRaw, 'accu') || str_contains($categoryRaw, 'aki')) {
                        $catVal = 'ganti_accu';
                    } elseif (str_contains($categoryRaw, 'sparepart')) {
                        $catVal = 'sparepart';
                    }

                    $currentComplaint = [
                        'num' => $m[1],
                        'category' => $catVal,
                        'category_label' => $m[2],
                        'title' => '',
                        'cost' => 0,
                        'desc' => '',
                    ];
                } elseif ($currentComplaint && str_starts_with($trimmed, 'Judul: ')) {
                    $titleLine = substr($trimmed, 7);
                    if (preg_match('/^(.*?)\s*\((?:Estimasi|Biaya):\s*Rp\s*([0-9.,]+)\)$/i', $titleLine, $cm)) {
                        $currentComplaint['title'] = trim($cm[1]);
                        $cleanCost = (float) str_replace(['.', ','], '', $cm[2]);
                        $currentComplaint['cost'] = $cleanCost;
                    } else {
                        $currentComplaint['title'] = trim($titleLine);
                    }
                } elseif ($currentComplaint && str_starts_with($trimmed, 'Uraian: ')) {
                    $currentComplaint['desc'] = substr($trimmed, 8);
                } elseif ($currentComplaint) {
                    $currentComplaint['desc'] .= (! empty($currentComplaint['desc']) ? "\n" : '').$trimmed;
                }
            }

            if ($currentComplaint) {
                $complaints[] = $currentComplaint;
            }

            // Fallback if no complaint block was parsed but it's a repair request
            if (empty($complaints) && $cr->request_type === 'perbaikan_servis') {
                $cleanDesc = $cr->description;
                // Remove all metadata bracket tags
                $cleanDesc = preg_replace('/\[.*?\]\n?/', '', $cleanDesc);
                $complaints[] = [
                    'num' => 1,
                    'category' => 'service_berkala',
                    'category_label' => 'Service Berkala',
                    'title' => $cr->title,
                    'cost' => (float) $cr->estimated_cost,
                    'desc' => trim($cleanDesc),
                ];
            }

            // Clean fallback description for other types
            $cleanOtherDesc = preg_replace('/\[.*?\]\n?/', '', $cr->description);

            $cr->parsed_meta = [
                'pic_name' => $picName,
                'pic_phone' => $picPhone,
                'operational_status' => $opStatus,
                'unit_position' => $unitPos,
                'physical_notes' => $physNotes,
                'workshop_phone' => $wsPhone,
                'mechanic_name' => $mechName,
                'mechanic_phone' => $mechPhone,
                'complaints' => $complaints,
                'clean_other_desc' => trim($cleanOtherDesc),
            ];

            return $cr;
        });

        $clientRequestsData = $clientRequests->mapWithKeys(function ($cr) {
            $meta = $cr->parsed_meta ?? [];
            $proofFiles = is_string($cr->proof_files) ? json_decode($cr->proof_files, true) : ($cr->proof_files ?? []);
            if (! is_array($proofFiles)) {
                $proofFiles = [];
            }

            $complaintPhotosMap = [];
            $vehiclePhotos = [];
            $generalProofs = [];

            foreach ($proofFiles as $pf) {
                if (empty($pf['path'])) {
                    continue;
                }
                $url = asset('storage/'.$pf['path']);
                $name = $pf['name'] ?? basename($pf['path']);
                $isImg = (bool) preg_match('/\.(jpg|jpeg|png|webp)$/i', $pf['path']);
                $item = [
                    'path' => $pf['path'],
                    'url' => $url,
                    'name' => $name,
                    'size' => $pf['size'] ?? 0,
                    'is_image' => $isImg,
                ];

                if (isset($pf['complaint_index'])) {
                    $complaintPhotosMap[$pf['complaint_index']][] = $item;
                } elseif (str_contains($name, 'Foto Tampak Depan')) {
                    $vehiclePhotos['photo_front'] = $item;
                } elseif (str_contains($name, 'Foto Tampak Samping Kanan')) {
                    $vehiclePhotos['photo_right'] = $item;
                } elseif (str_contains($name, 'Foto Tampak Samping Kiri')) {
                    $vehiclePhotos['photo_left'] = $item;
                } elseif (str_contains($name, 'Foto Tampak Belakang')) {
                    $vehiclePhotos['photo_behind'] = $item;
                } else {
                    $generalProofs[] = $item;
                }
            }

            return [$cr->id => [
                'id' => $cr->id,
                'vehicle_id' => $cr->vehicle_id,
                'vehicle_plate' => $cr->vehicle ? $cr->vehicle->plate_number : '',
                'vehicle_label' => $cr->vehicle ? $cr->vehicle->plate_number.' — '.$cr->vehicle->model_name.' ('.($cr->vehicle->location ? $cr->vehicle->location->name : 'Kebun').')' : '',
                'request_type' => $cr->request_type,
                'title' => $cr->title,
                'cost' => (float) $cr->estimated_cost,
                'odometer_km' => $cr->odometer_km,
                'driver_name' => $cr->driver_name,
                'driver_phone' => $cr->driver_phone,
                'pic_name' => $meta['pic_name'] ?? '',
                'pic_phone' => $meta['pic_phone'] ?? '',
                'operational_status' => $meta['operational_status'] ?? '',
                'unit_position' => $meta['unit_position'] ?? '',
                'physical_notes' => $meta['physical_notes'] ?? '',
                'workshop_name' => $cr->workshop_name ?: '',
                'workshop_phone' => $meta['workshop_phone'] ?? '',
                'mechanic_name' => $meta['mechanic_name'] ?? '',
                'mechanic_phone' => $meta['mechanic_phone'] ?? '',
                'complaints' => $meta['complaints'] ?? [],
                'clean_other_desc' => $meta['clean_other_desc'] ?? '',
                'memo' => 'REQ-KLIEN-'.str_pad($cr->id, 4, '0', STR_PAD_LEFT),
                'complaint_photos_map' => $complaintPhotosMap,
                'vehicle_photos' => $vehiclePhotos,
                'general_proofs' => $generalProofs,
                'all_proof_files' => $proofFiles,
            ]];
        });

        $selectedRequestId = $request->client_request_id;

        return view('maintenance.create', compact('vehicles', 'selectedVehicleId', 'defaultType', 'clientRequests', 'clientRequestsData', 'selectedRequestId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:service_berkala,ganti_ban,ganti_accu,sparepart,motor_pengganti,lainnya',
            'date' => 'required|date',
            'odometer_km' => 'nullable|numeric',
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
            'item_name' => 'nullable|string',
            'qty' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'memo_number' => 'nullable|string',
            'description' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photo_front' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photo_right' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photo_left' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photo_behind' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'proof_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'existing_proof_files' => 'nullable|string',
        ]);

        $uploadedFiles = [];

        // 0. Include any existing / auto-filled proof files from client request
        if (! empty($request->existing_proof_files)) {
            $existing = json_decode($request->existing_proof_files, true);
            if (is_array($existing)) {
                foreach ($existing as $exFile) {
                    if (! empty($exFile['path'])) {
                        $uploadedFiles[] = [
                            'path' => $exFile['path'],
                            'name' => $exFile['name'] ?? basename($exFile['path']),
                            'complaint_index' => isset($exFile['complaint_index']) ? (int) $exFile['complaint_index'] : null,
                            'size' => $exFile['size'] ?? 0,
                        ];
                    }
                }
            }
        }

        // 1. Extract 4 standard vehicle photos
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
                    $path = $file->store('maintenance_proofs', 'public');
                    $uploadedFiles[] = [
                        'path' => $path,
                        'name' => "{$label} - ".$file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ];
                }
            }
        }

        // 2. Extract global proof files
        if ($request->hasFile('proof_files')) {
            foreach ($request->file('proof_files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('maintenance_proofs', 'public');
                    $uploadedFiles[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ];
                }
            }
        }

        // 3. Extract individual complaint photos (complaint_photos_0, complaint_photos_1, etc.)
        foreach ($request->allFiles() as $key => $files) {
            if (str_starts_with($key, 'complaint_photos_')) {
                $complaintNum = str_replace('complaint_photos_', '', $key);
                $complaintIdx = is_numeric($complaintNum) ? ((int) $complaintNum + 1) : $complaintNum;
                $fileList = is_array($files) ? $files : [$files];
                foreach ($fileList as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('maintenance_proofs', 'public');
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

        // 4. Extract receipt file
        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', 'public');
        }

        // 5. Build description with rich context (PIC, Status, Bengkel, Mekanik) if available
        $description = $validated['description'] ?? '';

        $metaSections = [];
        if (! empty($validated['pic_name'])) {
            $metaSections[] = "[PIC: {$validated['pic_name']}".(! empty($validated['pic_phone']) ? " - Telp: {$validated['pic_phone']}" : '').']';
        }
        if (! empty($validated['workshop_name']) || ! empty($validated['workshop_phone']) || ! empty($validated['mechanic_name'])) {
            $bengkelInfo = [];
            if (! empty($validated['workshop_name'])) {
                $bengkelInfo[] = "Bengkel: {$validated['workshop_name']}";
            }
            if (! empty($validated['workshop_phone'])) {
                $bengkelInfo[] = "Telp: {$validated['workshop_phone']}";
            }
            if (! empty($validated['mechanic_name'])) {
                $bengkelInfo[] = "Mekanik: {$validated['mechanic_name']}".(! empty($validated['mechanic_phone']) ? " ({$validated['mechanic_phone']})" : '');
            }
            $metaSections[] = '['.implode(' | ', $bengkelInfo).']';
        }
        if (! empty($validated['vehicle_operational_status']) || ! empty($validated['vehicle_current_location'])) {
            $statusLabels = [
                'masih_bisa_jalan' => 'Masih Bisa Jalan Normal / Pelan',
                'mogok_total' => 'Mogok Total / Tidak Bisa Dihidupkan',
                'rusak_tidak_aman' => 'Bisa Hidup Tapi Berbahaya / Tidak Aman',
                'sudah_di_bengkel' => 'Unit Sudah Masuk / Menginap di Bengkel',
                'unit_hilang' => 'Unit Hilang / Tidak di Tempat (TLO)',
            ];
            $opLabel = $statusLabels[$validated['vehicle_operational_status']] ?? $validated['vehicle_operational_status'];
            $techInfo = [];
            if ($opLabel) {
                $techInfo[] = "Kondisi: {$opLabel}";
            }
            if (! empty($validated['vehicle_current_location'])) {
                $techInfo[] = "Lokasi: {$validated['vehicle_current_location']}";
            }
            if (! empty($validated['vehicle_condition_notes'])) {
                $techInfo[] = "Catatan Fisik: {$validated['vehicle_condition_notes']}";
            }
            $metaSections[] = '['.implode(' | ', $techInfo).']';
        }

        if (! empty($metaSections)) {
            $description = implode("\n", $metaSections)."\n\n".$description;
        }

        $record = MaintenanceRecord::create([
            'vehicle_id' => $validated['vehicle_id'],
            'type' => $validated['type'],
            'date' => $validated['date'],
            'odometer_km' => $validated['odometer_km'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_phone' => $validated['driver_phone'] ?? null,
            'pic_name' => $validated['pic_name'] ?? null,
            'pic_phone' => $validated['pic_phone'] ?? null,
            'workshop_name' => $validated['workshop_name'] ?? null,
            'workshop_phone' => $validated['workshop_phone'] ?? null,
            'mechanic_name' => $validated['mechanic_name'] ?? null,
            'mechanic_phone' => $validated['mechanic_phone'] ?? null,
            'vehicle_operational_status' => $validated['vehicle_operational_status'] ?? null,
            'vehicle_current_location' => $validated['vehicle_current_location'] ?? null,
            'vehicle_condition_notes' => $validated['vehicle_condition_notes'] ?? null,
            'item_name' => $validated['item_name'] ?? 'Pekerjaan Pemeliharaan / Servis',
            'qty' => $validated['qty'] ?? 1,
            'cost' => $validated['cost'] ?? 0,
            'memo_number' => $validated['memo_number'] ?? null,
            'description' => trim($description),
            'receipt_file' => $receiptPath,
            'proof_files' => ! empty($uploadedFiles) ? $uploadedFiles : null,
        ]);

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
            'expense_number' => 'EXP-'.date('Ymd').'-'.str_pad($record->id, 4, '0', STR_PAD_LEFT),
            'memo_number' => $record->memo_number,
            'transaction_date' => $record->date,
            'category' => $categoryMap[$record->type] ?? 'service_berkala',
            'title' => ($record->item_name ?: $record->type_label).' - '.($vehicle ? $vehicle->plate_number : ''),
            'amount' => $record->cost,
            'recipient_vendor' => $record->workshop_name ?: 'Bengkel / Vendor Rekanan',
            'description' => $record->description,
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Catatan pemeliharaan dan pengeluaran kas berhasil disimpan!');
    }

    public function show(MaintenanceRecord $maintenance)
    {
        $maintenance->load(['vehicle.location', 'vehicle.vendor']);
        $expense = Expense::where('maintenance_record_id', $maintenance->id)->first();

        // Extract files
        $files = is_array($maintenance->proof_files)
            ? $maintenance->proof_files
            : (is_string($maintenance->proof_files) ? json_decode($maintenance->proof_files, true) : []);
        if (! is_array($files)) {
            $files = [];
        }

        $vehicleAnglePhotos = [];
        $complaintPhotos = [];
        $generalDocs = [];

        foreach ($files as $f) {
            $name = $f['name'] ?? basename($f['path'] ?? '');
            if (str_contains($name, 'Foto Tampak Depan') || str_contains($name, 'Tampak Depan')) {
                $vehicleAnglePhotos['front'] = $f;
            } elseif (str_contains($name, 'Foto Tampak Samping Kanan') || str_contains($name, 'Samping Kanan')) {
                $vehicleAnglePhotos['right'] = $f;
            } elseif (str_contains($name, 'Foto Tampak Samping Kiri') || str_contains($name, 'Samping Kiri')) {
                $vehicleAnglePhotos['left'] = $f;
            } elseif (str_contains($name, 'Foto Tampak Belakang') || str_contains($name, 'Tampak Belakang')) {
                $vehicleAnglePhotos['behind'] = $f;
            } elseif (str_starts_with($name, 'Foto Keluhan #') || str_starts_with($name, 'Foto Item #') || isset($f['complaint_index'])) {
                $complaintPhotos[] = $f;
            } else {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $complaintPhotos[] = $f;
                } else {
                    $generalDocs[] = $f;
                }
            }
        }

        // Parse structured metadata and multi-complaint blocks from description
        $normalizedDesc = str_replace("\r\n", "\n", $maintenance->description ?? '');
        $descLines = explode("\n", $normalizedDesc);

        $structuredData = [
            'pic_name' => $maintenance->pic_name ?? '',
            'pic_phone' => $maintenance->pic_phone ?? '',
            'operational_status' => $maintenance->vehicle_operational_status ?? '',
            'unit_position' => $maintenance->vehicle_current_location ?? '',
            'physical_notes' => $maintenance->vehicle_condition_notes ?? '',
            'workshop_phone' => $maintenance->workshop_phone ?? '',
            'mechanic_name' => $maintenance->mechanic_name ?? '',
            'mechanic_phone' => $maintenance->mechanic_phone ?? '',
        ];

        $complaintBlocks = [];
        $cleanDescLines = [];
        $currentComplaint = null;

        foreach ($descLines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            if (str_starts_with($trimmed, '[PIC Pengaju:') || str_starts_with($trimmed, '[PIC:')) {
                $content = trim($trimmed, '[]');
                if (preg_match('/PIC(?: Pengaju)?:\s*([^-]+?)(?:\s*-\s*Telp:\s*(.*))?$/i', $content, $pm)) {
                    if (empty($structuredData['pic_name'])) {
                        $structuredData['pic_name'] = trim($pm[1] ?? '');
                    }
                    if (empty($structuredData['pic_phone'])) {
                        $structuredData['pic_phone'] = trim($pm[2] ?? '');
                    }
                }
            } elseif (str_starts_with($trimmed, '[Kondisi Operasional:') || str_contains($trimmed, 'Kondisi Operasional:') || str_starts_with($trimmed, '[Kondisi:')) {
                $content = trim($trimmed, '[]');
                $parts = explode('|', $content);
                foreach ($parts as $part) {
                    $partTrim = trim($part);
                    if (str_starts_with($partTrim, 'Kondisi Operasional:') && empty($structuredData['operational_status'])) {
                        $structuredData['operational_status'] = trim(substr($partTrim, 20));
                    } elseif (str_starts_with($partTrim, 'Kondisi:') && empty($structuredData['operational_status'])) {
                        $structuredData['operational_status'] = trim(substr($partTrim, 8));
                    } elseif ((str_starts_with($partTrim, 'Posisi Unit:') || str_starts_with($partTrim, 'Lokasi:')) && empty($structuredData['unit_position'])) {
                        $structuredData['unit_position'] = trim(substr($partTrim, strpos($partTrim, ':') + 1));
                    } elseif (str_starts_with($partTrim, 'Catatan Fisik:') && empty($structuredData['physical_notes'])) {
                        $structuredData['physical_notes'] = trim(substr($partTrim, 14));
                    }
                }
            } elseif (str_starts_with($trimmed, '[Telp Bengkel:') || str_contains($trimmed, 'Telp Bengkel:') || str_contains($trimmed, 'Mekanik:') || str_starts_with($trimmed, '[Bengkel:')) {
                $content = trim($trimmed, '[]');
                $parts = explode('|', $content);
                foreach ($parts as $part) {
                    $partTrim = trim($part);
                    if ((str_starts_with($partTrim, 'Telp Bengkel:') || str_starts_with($partTrim, 'Telp:')) && empty($structuredData['workshop_phone'])) {
                        $structuredData['workshop_phone'] = trim(substr($partTrim, strpos($partTrim, ':') + 1));
                    } elseif (str_starts_with($partTrim, 'Mekanik:') && empty($structuredData['mechanic_name'])) {
                        $mContent = trim(substr($partTrim, 8));
                        if (preg_match('/^(.*?)\s*\(Telp:\s*(.*?)\)$/i', $mContent, $mm)) {
                            $structuredData['mechanic_name'] = trim($mm[1]);
                            $structuredData['mechanic_phone'] = trim($mm[2]);
                        } else {
                            $structuredData['mechanic_name'] = $mContent;
                        }
                    }
                }
            } elseif (preg_match('/^\[(?:Item|Keluhan) #(\d+): (.*?)\]$/', $trimmed, $m)) {
                if ($currentComplaint) {
                    $complaintBlocks[] = $currentComplaint;
                }
                $currentComplaint = [
                    'index' => (int) $m[1],
                    'category' => $m[2],
                    'title' => '',
                    'cost' => '',
                    'description' => '',
                ];
            } elseif ($currentComplaint) {
                if (str_starts_with($trimmed, 'Judul:')) {
                    $titleRaw = trim(substr($trimmed, 6));
                    if (preg_match('/^(.*?)(?:\s*\((?:Biaya|Estimasi):\s*Rp\s*([\d\.,]+)\))?$/i', $titleRaw, $tm)) {
                        $currentComplaint['title'] = trim($tm[1]);
                        $currentComplaint['cost'] = trim($tm[2] ?? '');
                    } else {
                        $currentComplaint['title'] = $titleRaw;
                    }
                } elseif (str_starts_with($trimmed, 'Uraian:')) {
                    $currentComplaint['description'] = trim(substr($trimmed, 7));
                } else {
                    $currentComplaint['description'] .= ($currentComplaint['description'] ? "\n" : '').$trimmed;
                }
            } else {
                $cleanDescLines[] = $line;
            }
        }

        if ($currentComplaint) {
            $complaintBlocks[] = $currentComplaint;
        }

        $cleanDescription = trim(implode("\n", $cleanDescLines));

        return view('maintenance.show', compact(
            'maintenance',
            'expense',
            'vehicleAnglePhotos',
            'complaintPhotos',
            'generalDocs',
            'structuredData',
            'complaintBlocks',
            'cleanDescription'
        ));
    }

    public function destroy(MaintenanceRecord $maintenance)
    {
        // Delete attached expense if exists
        Expense::where('maintenance_record_id', $maintenance->id)->delete();
        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Catatan pemeliharaan berhasil dihapus!');
    }
}
