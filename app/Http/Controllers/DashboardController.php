<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Pilihan periode: kunci => [label chip, frasa untuk kalimat]. */
    private const PERIODE = [
        '7h' => ['chip' => '7 hari', 'teks' => '7 hari terakhir'],
        '30h' => ['chip' => '30 hari', 'teks' => '30 hari terakhir'],
        'bulan_ini' => ['chip' => 'Bulan ini', 'teks' => 'bulan ini'],
        'tahun_ini' => ['chip' => 'Tahun ini', 'teks' => 'tahun ini'],
    ];

    private const PERIODE_DEFAULT = '30h';

    /** Status disposisi: kunci => label. */
    private const STATUS = [
        'menunggu' => 'Menunggu',
        'diterima' => 'Diterima',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
    ];

    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Filter dari query string (nilai di luar daftar dianggap tidak ada)
        $periode = $this->pilih($request, 'periode', self::PERIODE, self::PERIODE_DEFAULT);
        $status = $this->pilih($request, 'status', self::STATUS, null);

        // Base stats (agendaris & pimpinan see all)
        if ($user->canViewAllSurat()) {
            $suratMasuk = SuratMasuk::count();
            $suratKeluar = SuratKeluar::count();
            $disposisiPending = Disposisi::where('status', 'menunggu')->count();
            $disposisiSelesai = Disposisi::where('status', 'selesai')->count();
        } else {
            // Bidang only sees disposisi assigned to them
            $suratMasuk = SuratMasuk::visibleTo($user)->count();

            $suratKeluar = SuratKeluar::where('dibuat_oleh', $user->id)->count();

            $disposisiPending = Disposisi::where('status', 'menunggu')
                ->where(function ($q) use ($user) {
                    $q->where('kepada_user_id', $user->id)
                      ->orWhere('unit_id', $user->unit_id);
                })->count();

            $disposisiSelesai = Disposisi::where('status', 'selesai')
                ->where(function ($q) use ($user) {
                    $q->where('kepada_user_id', $user->id)
                      ->orWhere('unit_id', $user->unit_id);
                })->count();
        }

        // Recent disposisi for current user (opsional disaring status)
        $recentDisposisi = Disposisi::with(['suratMasuk', 'dariUser'])
            ->where(function ($q) use ($user) {
                $q->where('kepada_user_id', $user->id)
                  ->orWhere('unit_id', $user->unit_id);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', array_merge(
            compact(
                'suratMasuk',
                'suratKeluar',
                'disposisiPending',
                'disposisiSelesai',
                'recentDisposisi',
                'periode',
                'status',
            ),
            $this->dataGrafik($user, $periode, $status),
            [
                'periodeOptions' => self::PERIODE,
                'statusOptions' => self::STATUS,
            ],
        ));
    }

    /**
     * Data grafik & angka periode. Cakupan data sama dengan stat di atas:
     * agendaris & pimpinan melihat semua, bidang hanya miliknya.
     * Filter status hanya menyaring deret "disposisi" di grafik tren; donat
     * status selalu menampilkan semua status supaya bisa dipakai untuk memilih.
     */
    private function dataGrafik(User $user, string $periode, ?string $status): array
    {
        $semua = $user->canViewAllSurat();
        $hariIni = Carbon::today();

        [$awal, $perBulan] = match ($periode) {
            '7h' => [$hariIni->copy()->subDays(6), false],
            'bulan_ini' => [$hariIni->copy()->startOfMonth(), false],
            'tahun_ini' => [$hariIni->copy()->startOfYear(), true],
            default => [$hariIni->copy()->subDays(29), false],
        };

        // Satu bucket per hari (atau per bulan untuk "tahun ini")
        $format = $perBulan ? 'Y-m' : 'Y-m-d';
        $kunci = [];
        $label = [];
        $t = $awal->copy();
        while ($t->lte($hariIni)) {
            $kunci[] = $t->format($format);
            $label[] = $t->copy()->locale('id')->isoFormat($perBulan ? 'MMM' : 'D MMM');
            $perBulan ? $t->addMonthNoOverflow() : $t->addDay();
        }

        $hitung = function ($tanggal) use ($kunci, $format) {
            $isi = array_fill_keys($kunci, 0);
            foreach ($tanggal as $tgl) {
                $k = Carbon::parse($tgl)->format($format);
                if (isset($isi[$k])) {
                    $isi[$k]++;
                }
            }

            return array_values($isi);
        };

        $disposisi = fn () => Disposisi::query()->when(! $semua, fn ($q) => $q->where(
            fn ($w) => $w->where('kepada_user_id', $user->id)->orWhere('unit_id', $user->unit_id)
        ));

        $masuk = SuratMasuk::visibleTo($user)
            ->whereBetween('tanggal_diterima', [$awal->toDateString(), $hariIni->toDateString()])
            ->pluck('tanggal_diterima');

        $keluar = SuratKeluar::query()
            ->when(! $semua, fn ($q) => $q->where('dibuat_oleh', $user->id))
            ->whereBetween('tanggal_surat', [$awal->toDateString(), $hariIni->toDateString()])
            ->pluck('tanggal_surat');

        $disposisiTren = $disposisi()
            ->where('created_at', '>=', $awal)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->pluck('created_at');

        $perStatus = $disposisi()
            ->where('created_at', '>=', $awal)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $dataStatus = [];
        foreach (self::STATUS as $kunciStatus => $namaStatus) {
            $dataStatus[$kunciStatus] = (int) ($perStatus[$kunciStatus] ?? 0);
        }

        $seriMasuk = $hitung($masuk);
        $seriKeluar = $hitung($keluar);
        $seriDisposisi = $hitung($disposisiTren);

        return [
            'suratMasukPeriode' => array_sum($seriMasuk),
            'suratKeluarPeriode' => array_sum($seriKeluar),
            'disposisiSelesaiPeriode' => $disposisi()
                ->where('status', 'selesai')
                ->where('diselesaikan_at', '>=', $awal)
                ->count(),
            'perBulan' => $perBulan,
            'chart' => [
                'labels' => $label,
                'series' => [
                    'masuk' => $seriMasuk,
                    'keluar' => $seriKeluar,
                    'disposisi' => $seriDisposisi,
                ],
                'seriesLabels' => [
                    'masuk' => 'Surat masuk',
                    'keluar' => 'Surat keluar',
                    'disposisi' => 'Disposisi',
                ],
                'status' => $dataStatus,
                'statusLabels' => self::STATUS,
            ],
        ];
    }

    /** Ambil nilai query yang ada di daftar $boleh, kalau tidak pakai $default. */
    private function pilih(Request $request, string $key, array $boleh, ?string $default): ?string
    {
        $nilai = $request->query($key);

        return is_string($nilai) && array_key_exists($nilai, $boleh) ? $nilai : $default;
    }
}