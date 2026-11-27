<x-layouts.app title="Tambah Surat Masuk">

    <x-form-page-header title="Tambah Surat Masuk" subtitle="Catat surat masuk baru beserta file fisiknya"
        :back-url="route('surat-masuk.index')" back-label="Kembali ke daftar surat masuk" />

    @include('surat-masuk._form')

</x-layouts.app>