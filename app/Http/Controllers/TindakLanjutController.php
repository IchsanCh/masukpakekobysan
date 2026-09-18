<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\TindakLanjut;
use App\Models\TindakLanjutLampiran;
use App\Services\WhatsAppNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TindakLanjutController extends Controller
{
    /**
     * Batas ukuran tiap lampiran, dalam KB (Laravel validation 'max' pakai KB).
     */
    private const MAX_FILE_KB = 10240;

    public function index(Request $request): View
    {
        $user = $request->user();

        // Disposisi aktif (belum selesai/ditolak) yang ditujukan langsung ke user ini —
        // sama persis kriteria canBeActedBy(), biar konsisten sama yang bisa ditindaklanjuti.
        $pending = Disposisi::with(['suratMasuk', 'dariUser', 'unit', 'kepadaUser'])
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->where(function ($q) use ($user) {
                $q->where('kepada_user_id', $user->id)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('tipe_tujuan', 'unit')->where('unit_id', $user->unit_id);
                  });
            })
            ->orderByRaw('batas_waktu IS NULL')
            ->orderBy('batas_waktu')
            ->orderByDesc('created_at')
            ->get();

        $riwayat = TindakLanjut::with(['disposisi.suratMasuk', 'lampiran'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('tindak-lanjut.index', compact('pending', 'riwayat'));
    }

    public function store(Request $request, Disposisi $disposisi, WhatsAppNotifier $notifier): RedirectResponse
    {
        $user = $request->user();

        // Cuma penerima langsung disposisi ini yang boleh nulis tindak lanjut
        // (gak ada jalur "atas nama pimpinan" di sini, beda dari forward()).
        abort_unless($disposisi->canBeActedBy($user), 403);
        abort_if($disposisi->isTerminal(), 422, 'Disposisi ini sudah selesai/ditolak, tidak bisa ditambah tindak lanjut lagi.');

        $validated = $request->validate([
            'keterangan' => 'required|string',
            'lampiran' => 'nullable|array|max:5',
            'lampiran.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:' . self::MAX_FILE_KB,
        ], [
            'lampiran.max' => 'Maksimal 5 lampiran per tindak lanjut.',
            'lampiran.*.max' => 'Ukuran tiap lampiran maksimal ' . (self::MAX_FILE_KB / 1024) . ' MB.',
            'lampiran.*.mimes' => 'Lampiran harus berformat PDF, gambar, Word, atau Excel.',
        ]);

        $tindakLanjut = TindakLanjut::create([
            'disposisi_id' => $disposisi->id,
            'user_id' => $user->id,
            'keterangan' => $validated['keterangan'],
        ]);

        foreach ($request->file('lampiran', []) as $file) {
            $path = $file->store('tindak-lanjut', 'public');

            TindakLanjutLampiran::create([
                'tindak_lanjut_id' => $tindakLanjut->id,
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'ukuran_bytes' => $file->getSize(),
            ]);
        }

        // Staf: tindak lanjut dianggap final -> disposisi langsung selesai.
        // Selain staf (pimpinan/kabid/sekretariat/agendaris): baru jadi diproses,
        // mereka yang menandai selesai sendiri lewat tombol "Tandai Selesai" yang sudah ada.
        $targetStatus = $user->hasRole('staf') ? 'selesai' : 'diproses';

        $update = [
            'status' => $targetStatus,
            'dibaca_at' => $disposisi->dibaca_at ?? now(),
        ];

        if ($targetStatus === 'selesai') {
            $update['diselesaikan_at'] = now();
        }

        $disposisi->update($update);
        $disposisi->suratMasuk->refreshStatusDisposisi();

        if ($targetStatus === 'selesai') {
            $notifier->notifyDisposisiSelesai($disposisi, $user, $tindakLanjut->keterangan, $tindakLanjut->id);
        }

        return redirect()->route('disposisi.show', $disposisi->surat_masuk_id)
            ->with('success', 'Tindak lanjut berhasil ditambahkan.');
    }
}