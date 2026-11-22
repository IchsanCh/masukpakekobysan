<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $query = Unit::withCount('users');

        if ($search = $request->input('search')) {
            $query->where('nama_unit', 'like', "%{$search}%");
        }

        $units = $query->orderBy('nama_unit')->paginate(15)->withQueryString();

        // Untuk edit modal: jika ada ?edit=ID, load unit tsb
        $editUnit = null;
        if ($editId = $request->input('edit')) {
            $editUnit = Unit::find($editId);
        }

        return view('units.index', compact('units', 'editUnit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:255|unique:units,nama_unit',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:255|unique:units,nama_unit,' . $unit->id,
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->users()->count() > 0) {
            return back()->with('error', 'Unit tidak bisa dihapus karena masih memiliki pengguna.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil dihapus.');
    }
}