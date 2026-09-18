<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function index(): View
    {
        $fonnte = Configuration::getGroup('fonnte')->keyBy('key');
        $waTemplates = Configuration::getGroup('wa_template')->keyBy('key');
        $scheduler = Configuration::getGroup('scheduler')->keyBy('key');

        return view('configuration.index', compact('fonnte', 'waTemplates', 'scheduler'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'values' => 'required|array',
            'values.*' => 'nullable|string',
        ]);

        foreach ($validated['values'] as $key => $value) {
            // Hanya izinkan update key yang memang sudah terdaftar di tabel configurations
            // (bukan sembarang key dari request), biar gak bisa nyuntik key baru dari luar.
            if (Configuration::where('key', $key)->exists()) {
                // Browser (terutama Windows) suka ngirim Enter dari textarea sebagai \r\n,
                // bukan cuma \n. Normalisasi di sini biar yang kesimpen di DB selalu \n polos.
                $normalized = $value !== null ? str_replace(["\r\n", "\r"], "\n", $value) : '';
                Configuration::setValue($key, $normalized);
            }
        }

        return redirect()->route('configuration.index')
            ->with('success', 'Konfigurasi berhasil disimpan.');
    }

    /**
     * Proxy ke Fonnte API buat cek status koneksi device — dipanggil via fetch()
     * dari halaman konfigurasi. Token gak pernah keekspos ke browser karena
     * request-nya dari server, bukan langsung dari JS.
     */
    public function checkFonnteStatus(): JsonResponse
    {
        $token = Configuration::getValue('fonnte_token');

        if (empty($token)) {
            return response()->json([
                'status' => false,
                'reason' => 'Token Fonnte belum diisi.',
            ]);
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->timeout(10)
                ->post('https://api.fonnte.com/device');

            if (! $response->successful()) {
                return response()->json([
                    'status' => false,
                    'reason' => 'Fonnte membalas dengan error (HTTP ' . $response->status() . ').',
                ]);
            }

            return response()->json($response->json());
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'reason' => 'Gagal menghubungi Fonnte, cek koneksi internet server.',
            ]);
        }
    }
}