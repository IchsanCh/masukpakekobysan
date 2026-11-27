<x-layouts.app title="Tambah Surat Keluar">

    <x-form-page-header title="Tambah Surat Keluar" subtitle="Catat surat keluar baru"
        :back-url="route('surat-keluar.index')" back-label="Kembali ke daftar surat keluar" />

    @include('surat-keluar._form', ['suggestedNomor' => $suggestedNomor])

</x-layouts.app>