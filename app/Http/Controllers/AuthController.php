<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali di Sistem Monitoring Kendaraan Koperasi Swadharma!');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem.');
    }

    public function usersIndex(Request $request)
    {
        $perPage = in_array((int) $request->get('per_page', 10), [10, 15, 25, 50, 100]) ? (int) $request->get('per_page', 10) : 10;

        $query = User::with(['vendor', 'client'])->orderBy('id', 'asc');

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        $users = $query->paginate($perPage)->withQueryString();
        $vendors = Vendor::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        $stats = [
            'total' => User::count(),
            'internal' => User::where('category', 'internal')->count(),
            'klien' => User::where('category', 'klien')->count(),
        ];

        return view('users.index', compact('users', 'vendors', 'clients', 'perPage', 'stats'));
    }

    public function createUser(Request $request)
    {
        $vendors = Vendor::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $defaultCategory = $request->get('category', 'internal');

        return view('users.create', compact('vendors', 'clients', 'defaultCategory'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'category' => 'required|in:internal,klien',
            'client_id' => 'nullable|exists:clients,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        if ($validated['category'] === 'klien' && empty($validated['client_id']) && empty($validated['vendor_id'])) {
            return back()->withInput()->withErrors(['client_id' => 'Silakan pilih Perusahaan Klien untuk akun portal klien.']);
        }

        $clientId = null;
        $vendorId = null;
        if ($validated['category'] === 'klien') {
            $clientId = $validated['client_id'] ?: Vendor::where('id', $validated['vendor_id'])->value('client_id');
            $vendorId = $validated['vendor_id'] ?: Vendor::where('client_id', $clientId)->value('id');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'category' => $validated['category'],
            'vendor_id' => $vendorId,
            'client_id' => $clientId,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun pengguna ('.($validated['category'] === 'klien' ? 'Klien' : 'Internal Koperasi Swadharma').') berhasil ditambahkan!');
    }

    public function destroyUser(User $user)
    {
        if (User::count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus user satu-satunya.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
