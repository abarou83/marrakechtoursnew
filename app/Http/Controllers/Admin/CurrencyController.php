<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderByDesc('is_default')->orderBy('code')->get();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:3', 'unique:currencies,code'],
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['nullable', 'string', 'max:8'],
            'rate_to_base' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_default'] = false;

        Currency::create($data);

        return redirect()->route('admin.currencies.index')->with('success', 'Devise créée.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['nullable', 'string', 'max:8'],
            'rate_to_base' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $currency->update($data);

        return redirect()->route('admin.currencies.index')->with('success', 'Devise mise à jour.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'Impossible de supprimer la devise par défaut.');
        }
        $currency->delete();
        return back()->with('success', 'Devise supprimée.');
    }

    public function toggleActive(Currency $currency)
    {
        $currency->update(['is_active' => !$currency->is_active]);
        return back()->with('success', 'Statut de la devise mis à jour.');
    }

    public function setDefault(Currency $currency)
    {
        Currency::where('is_default', true)->update(['is_default' => false]);
        $currency->update(['is_default' => true, 'is_active' => true, 'rate_to_base' => $currency->rate_to_base]);
        // Also update session currency to new default if none selected
        if (!session()->has('currency')) {
            session(['currency' => $currency->code]);
        }
        return back()->with('success', 'Devise par défaut mise à jour.');
    }
}
