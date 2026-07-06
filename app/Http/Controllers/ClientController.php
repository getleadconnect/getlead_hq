<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get()->map(fn ($c) => [
            'id'           => $c->id,
            'business_id'  => $c->business_id,
            'name'         => $c->name,
            'email'        => $c->email,
            'country_code' => $c->country_code,
            'mobile'       => $c->mobile,
        ])->values();

        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id'  => 'nullable|string|max:255',
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'country_code' => 'nullable|string|max:8',
            'mobile'       => 'nullable|string|max:20',
        ]);

        $client = Client::create([
            'business_id'  => $data['business_id'] ?? null,
            'name'         => $data['name'],
            'email'        => $data['email'] ?? null,
            'country_code' => $data['country_code'] ?: '+91',
            'mobile'       => $data['mobile'] ?? null,
            'created_by'   => Auth::guard('staff')->id(),
        ]);

        return response()->json(['ok' => true, 'client' => $this->row($client)]);
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'business_id'  => 'nullable|string|max:255',
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'country_code' => 'nullable|string|max:8',
            'mobile'       => 'nullable|string|max:20',
        ]);

        $client->update([
            'business_id'  => $data['business_id'] ?? null,
            'name'         => $data['name'],
            'email'        => $data['email'] ?? null,
            'country_code' => $data['country_code'] ?: '+91',
            'mobile'       => $data['mobile'] ?? null,
        ]);

        return response()->json(['ok' => true, 'client' => $this->row($client)]);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(['ok' => true]);
    }

    private function row(Client $c): array
    {
        return [
            'id'           => $c->id,
            'business_id'  => $c->business_id,
            'name'         => $c->name,
            'email'        => $c->email,
            'country_code' => $c->country_code,
            'mobile'       => $c->mobile,
        ];
    }
}
