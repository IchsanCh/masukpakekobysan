<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('unit');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $units = Unit::where('is_active', true)->orderBy('nama_unit')->get();
        $roles = ['agendaris', 'pimpinan', 'sekretariat', 'kabid', 'staf'];

        $editUser = null;
        if ($editId = $request->input('edit')) {
            $editUser = User::with('unit')->find($editId);
        }

        return view('users.index', compact('users', 'units', 'roles', 'editUser'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:50|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
            'no_wa'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'unit_id'   => 'required|exists:units,id',
            'peran'     => 'required|in:agendaris,pimpinan,sekretariat,kabid,staf',
        ]);

        User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'no_wa'     => $validated['no_wa'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'unit_id'   => $validated['unit_id'],
            'peran'     => $validated['peran'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'  => 'nullable|string|min:6|confirmed',
            'no_wa'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'unit_id'   => 'required|exists:units,id',
            'peran'     => 'required|in:agendaris,pimpinan,sekretariat,kabid,staf',
        ]);

        $userData = [
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'no_wa'     => $validated['no_wa'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'unit_id'   => $validated['unit_id'],
            'peran'     => $validated['peran'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}