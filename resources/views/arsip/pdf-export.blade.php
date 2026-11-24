<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 2px; }
        .subtitle { text-align: center; font-size: 11px; color: #555; margin-bottom: 4px; }
        .meta { margin: 14px 0; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 5px 7px; text-align: left; vertical-align: top; }
        th { background: #eee; font-weight: bold; }
        tr:nth-child(even) { background: #fafafa; }
        .footer { margin-top: 30px; font-size: 10px; text-align: right; }
    </style>
</head>
<body>
    @if ($tab === 'riwayat')
        {{-- Judul mengikuti isi: "Berita Acara Pemusnahan" hanya kalau isinya murni pemusnahan. --}}
        <h1>
            @if ($keputusan === 'musnah')
                Berita Acara Pemusnahan Arsip
            @elseif ($keputusan === 'permanen')
                Daftar Penetapan Arsip Permanen
            @else
                Riwayat Keputusan Arsip
            @endif
        </h1>
        <p class="subtitle">DPMPTSP Kabupaten Pekalongan</p>
    @else
        <h1>Laporan Arsip</h1>
        <p class="subtitle">DPMPTSP Kabupaten Pekalongan</p>
    @endif

    <div class="meta">
        Periode: {{ $tanggalDari ?? 'Semua' }} s.d. {{ $tanggalSampai ?? 'Semua' }}<br>
        Dicetak oleh: {{ $dicetakOleh }} pada {{ now()->format('d-m-Y H:i') }}
    </div>

    @if (empty($rows))
        <p>Tidak ada data untuk periode/filter ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach (array_keys($rows[0]) as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $val)
                            <td>{{ $val }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($tab === 'riwayat')
        <div class="footer">
            @if ($keputusan === 'musnah')
                Total {{ count($rows) }} arsip dimusnahkan pada periode ini.
            @elseif ($keputusan === 'permanen')
                Total {{ count($rows) }} arsip ditetapkan permanen pada periode ini.
            @else
                Total {{ count($rows) }} keputusan arsip pada periode ini.
            @endif
        </div>
    @endif
</body>
</html>