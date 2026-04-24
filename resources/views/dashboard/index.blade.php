<x-layouts.app title="Dashboard">
    {{-- Stats --}}
    <div class="stats shadow w-full bg-base-100">
        <x-stat title="Surat Masuk" :value="$suratMasuk" icon="inbox" />
        <x-stat title="Surat Keluar" :value="$suratKeluar" icon="send" />
        <x-stat title="Disposisi Menunggu" :value="$disposisiPending" icon="forward" description="Belum ditindaklanjuti" />
        <x-stat title="Disposisi Selesai" :value="$disposisiSelesai" icon="check-circle" />
    </div>

    {{-- Recent Disposisi --}}
    <x-card title="Disposisi Terbaru" class="mt-6">
        @if ($recentDisposisi->isEmpty())
            <p class="text-base-content/60">Belum ada disposisi.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Surat</th>
                            <th>Dari</th>
                            <th>Instruksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentDisposisi as $d)
                            <tr class="hover">
                                <td>{{ $d->created_at->format('d/m/Y') }}</td>
                                <td>{{ $d->suratMasuk->nomor_surat ?? '-' }}</td>
                                <td>{{ $d->dariUser->name ?? '-' }}</td>
                                <td class="max-w-xs truncate">{{ $d->instruksi }}</td>
                                <td>
                                    @switch($d->status)
                                        @case('menunggu')
                                            <span class="badge badge-warning badge-sm">Menunggu</span>
                                        @break

                                        @case('diterima')
                                            <span class="badge badge-info badge-sm">Diterima</span>
                                        @break

                                        @case('diproses')
                                            <span class="badge badge-primary badge-sm">Diproses</span>
                                        @break

                                        @case('selesai')
                                            <span class="badge badge-success badge-sm">Selesai</span>
                                        @break

                                        @case('ditolak')
                                            <span class="badge badge-error badge-sm">Ditolak</span>
                                        @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts.app>
