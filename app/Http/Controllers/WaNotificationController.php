<?php

namespace App\Http\Controllers;

use App\Models\WaNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = WaNotification::with(['user', 'suratMasuk', 'disposisi.suratMasuk']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pesan', 'like', "%{$search}%")
                  ->orWhere('no_wa_tujuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($tipe = $request->input('tipe')) {
            $query->where('tipe', $tipe);
        }

        if ($status = $request->input('status_kirim')) {
            $query->where('status_kirim', $status);
        }

        $notifications = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('notifikasi.index', compact('notifications'));
    }
}