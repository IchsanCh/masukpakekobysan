<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    // URL::forceScheme('https');
    View::composer(['components.sidebar', 'components.navbar'], function ($view) {
        static $badges = null; // sidebar & navbar sama-sama manggil, hitung sekali per request

        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($badges === null) {
            $disposisi = Disposisi::where('status', 'menunggu')
                ->where(function ($q) use ($user) {
                    $q->where(fn ($q2) => $q2->where('tipe_tujuan', 'unit')->where('unit_id', $user->unit_id))
                      ->orWhere(fn ($q2) => $q2->where('tipe_tujuan', 'personal')->where('kepada_user_id', $user->id));
                })
                ->count();

            $tinjau = $approval = 0;
            if ($user->hasRole('agendaris')) {
                $tinjau   = SuratMasuk::where('status_arsip', 'perlu_ditinjau')->count()
                          + SuratKeluar::where('status_arsip', 'perlu_ditinjau')->count();
                $approval = SuratMasuk::where('status_arsip', 'menunggu_musnah')->count()
                          + SuratKeluar::where('status_arsip', 'menunggu_musnah')->count();
            }

            $badges = [
                'badgeDisposisi'      => $disposisi,
                'badgeArsipTinjau'    => $tinjau,
                'badgeArsipApproval'  => $approval,
                'badgeTotal'          => $disposisi + $tinjau + $approval,
            ];
        }

        $view->with($badges);
    });
    }
}
