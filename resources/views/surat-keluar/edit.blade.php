<x-layouts.app title="Edit Surat Keluar">

    <x-form-page-header title="Edit Surat Keluar" :subtitle="$suratKeluar->nomor_surat"
        :back-url="route('surat-keluar.index')" back-label="Kembali ke daftar surat keluar" />

    @include('surat-keluar._form')

</x-layouts.app>