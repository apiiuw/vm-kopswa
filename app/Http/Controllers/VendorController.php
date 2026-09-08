<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::withCount(['vehicles', 'incomes', 'expenses']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('contract_number', 'like', "%{$s}%")
                  ->orWhere('client_name', 'like', "%{$s}%");
            });
        }

        $vendors = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();

        return view('vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:vendors,code',
            'contract_number' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Vendor::create($validated);

        return redirect()->route('vendors.index')->with('success', 'Data vendor baru berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:vendors,code,' . $vendor->id,
            'contract_number' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Data vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->vehicles()->exists()) {
            return back()->with('error', 'Vendor tidak dapat dihapus karena masih memiliki relasi data armada kendaraan.');
        }

        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus.');
    }

    // Switch active vendor filter in session
    public function switchVendor(Request $request)
    {
        $vendorId = $request->input('vendor_id');
        if ($vendorId === 'all' || empty($vendorId)) {
            session()->forget('active_vendor_id');
        } else {
            session(['active_vendor_id' => (int)$vendorId]);
        }

        return back()->with('success', 'Filter vendor aktif berhasil diperbarui.');
    }
}
