<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount(['contracts', 'users']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('npwp', 'like', "%{$s}%")
                    ->orWhere('pic_name', 'like', "%{$s}%");
            });
        }

        $clients = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:clients,code',
            'npwp' => 'nullable|string|max:50',
            'nitku' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'pic_name' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:120',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Data Master Klien berhasil ditambahkan.');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:clients,code,'.$client->id,
            'npwp' => 'nullable|string|max:50',
            'nitku' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'pic_name' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:120',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        // Sync basic client identity to linked contracts
        $client->contracts()->update([
            'name' => $client->name,
            'npwp' => $client->npwp ?? $client->contracts()->value('npwp'),
        ]);

        return redirect()->route('clients.index')->with('success', 'Data Master Klien berhasil diperbarui.');
    }

    public function destroy(Client $client)
    {
        if ($client->contracts()->exists()) {
            return back()->with('error', 'Klien tidak dapat dihapus karena masih memiliki kontrak sewa aktif.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Data Klien berhasil dihapus.');
    }
}
