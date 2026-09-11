<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Vendor;

class LocationController extends Controller
{
    /**
     * Store a new location (supports normal web request or AJAX/JSON).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'manager_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $location = Location::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Wilayah / Kebun berhasil ditambahkan.',
                'location' => [
                    'id' => $location->id,
                    'name' => $location->name,
                    'vendor_id' => $location->vendor_id,
                    'code' => $location->code,
                ],
            ]);
        }

        return back()->with('success', 'Wilayah / Kebun baru berhasil ditambahkan.');
    }

    /**
     * Get locations by vendor ID (JSON API for dynamic dropdowns).
     */
    public function byVendor(Vendor $vendor)
    {
        $locations = $vendor->locations()->orderBy('name')->get(['id', 'name', 'code', 'vendor_id']);
        return response()->json($locations);
    }

    /**
     * Update location details.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'manager_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $location->update($validated);

        return back()->with('success', 'Data Wilayah / Kebun berhasil diperbarui.');
    }

    /**
     * Delete a location.
     */
    public function destroy(Location $location)
    {
        if ($location->vehicles()->exists()) {
            return back()->with('error', 'Wilayah / Kebun tidak dapat dihapus karena masih ada unit kendaraan yang terdaftar pada lokasi ini.');
        }

        $location->delete();

        return back()->with('success', 'Wilayah / Kebun berhasil dihapus.');
    }
}
