<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiIntegrationController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::latest()->get();

        return view('integrations.index', compact('tokens'));
    }

    public function storeToken(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
        ]);

        $plainToken = 'kopswa_'.Str::random(40);
        ApiToken::create([
            'name' => $validated['name'],
            'token_hash' => hash('sha256', $plainToken),
            'plain_prefix' => substr($plainToken, 0, 14).'...',
            'abilities' => ['accounting:read'],
            'is_active' => true,
        ]);

        return redirect()->route('integrations.index')
            ->with('success', 'API Token berhasil dibuat!')
            ->with('generated_api_token', $plainToken);
    }

    public function destroyToken(ApiToken $apiToken)
    {
        $apiToken->delete();

        return redirect()->route('integrations.index')->with('success', 'API Token berhasil dicabut.');
    }
}
