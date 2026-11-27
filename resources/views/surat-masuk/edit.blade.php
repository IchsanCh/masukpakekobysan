<x-layouts.app title="Edit Surat Masuk">

    <x-form-page-header title="Edit Surat Masuk" :subtitle="$suratMasuk->nomor_surat"
        :back-url="route('surat-masuk.index')" back-label="Kembali ke daftar surat masuk" />

    @include('surat-masuk._form')

</x-layouts.app>