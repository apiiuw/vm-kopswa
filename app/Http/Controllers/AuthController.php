<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali di Sistem Monitoring Kendaraan PT SUP!');
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
        
        $query = User::with('vendor')->orderBy('id', 'asc');

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        $users = $query->paginate($perPage)->withQueryString();
        $vendors = \App\Models\Vendor::orderBy('name')->get();

        $stats = [
            'total' => User::count(),
            'internal' => User::where('category', 'internal')->count(),
            'klien' => User::where('category', 'klien')->count(),
        ];

        return view('users.index', compact('users', 'vendors', 'perPage', 'stats'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'category' => 'required|in:internal,klien',
            'vendor_id' => 'nullable|required_if:category,klien|exists:vendors,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'category' => $validated['category'],
            'vendor_id' => $validated['category'] === 'klien' ? $validated['vendor_id'] : null,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun pengguna (' . ($validated['category'] === 'klien' ? 'Klien' : 'Internal PT SUP') . ') berhasil ditambahkan!');
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
