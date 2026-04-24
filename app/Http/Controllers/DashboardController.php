<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Base stats (agendaris & pimpinan see all)
        if ($user->canViewAllSurat()) {
            $suratMasuk = SuratMasuk::count();
            $suratKeluar = SuratKeluar::count();
            $disposisiPending = Disposisi::where('status', 'menunggu')->count();
            $disposisiSelesai = Disposisi::where('status', 'selesai')->count();
        } else {
            // Bidang only sees disposisi assigned to them
            $unitIds = $user->units->pluck('id');

            $suratMasuk = SuratMasuk::whereHas('disposisi', function ($q) use ($user, $unitIds) {
                $q->where('kepada_user_id', $user->id)
                  ->orWhereIn('unit_id', $unitIds);
            })->count();

            $suratKeluar = SuratKeluar::where('dibuat_oleh', $user->id)->count();

            $disposisiPending = Disposisi::where('status', 'menunggu')
                ->where(function ($q) use ($user, $unitIds) {
                    $q->where('kepada_user_id', $user->id)
                      ->orWhereIn('unit_id', $unitIds);
                })->count();

            $disposisiSelesai = Disposisi::where('status', 'selesai')
                ->where(function ($q) use ($user, $unitIds) {
                    $q->where('kepada_user_id', $user->id)
                      ->orWhereIn('unit_id', $unitIds);
                })->count();
        }

        // Recent disposisi for current user
        $recentDisposisi = Disposisi::with(['suratMasuk', 'dariUser'])
            ->where(function ($q) use ($user) {
                $q->where('kepada_user_id', $user->id)
                  ->orWhereIn('unit_id', $user->units->pluck('id'));
            })
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'suratMasuk',
            'suratKeluar',
            'disposisiPending',
            'disposisiSelesai',
            'recentDisposisi',
        ));
    }
}
