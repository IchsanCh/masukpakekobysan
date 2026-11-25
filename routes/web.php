<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TindakLanjutController;
use App\Http\Controllers\ReferensiRetensiController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaNotificationController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Forgot & Reset Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Akun — semua peran boleh ubah data akunnya sendiri (nama, username, email,
    // password, no. WhatsApp). Sengaja di luar middleware role.
    Route::get('profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profil/nama', [ProfileController::class, 'updateName'])->name('profile.name');
    Route::put('profil/username', [ProfileController::class, 'updateUsername'])->name('profile.username');
    Route::put('profil/email', [ProfileController::class, 'updateEmail'])->name('profile.email');
    Route::put('profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('profil/no-wa', [ProfileController::class, 'updatePhone'])->name('profile.phone');

    // Master Data — hanya agendaris, modal-based (no create/edit pages)
    Route::middleware('role:agendaris')->group(function () {
        Route::resource('units', UnitController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('retensi', ReferensiRetensiController::class)->only(['index', 'store', 'update', 'destroy']);

        // Input & kelola surat masuk — tetap agendaris-only (petugas agenda)
        Route::resource('surat-masuk', SuratMasukController::class)->only([
            'create', 'store', 'edit', 'update', 'destroy',
        ]);

        // Konfigurasi (Fonnte, template WA, jadwal pengingat)
        Route::get('konfigurasi', [ConfigurationController::class, 'index'])->name('configuration.index');
        Route::put('konfigurasi', [ConfigurationController::class, 'update'])->name('configuration.update');
        Route::get('konfigurasi/fonnte-status', [ConfigurationController::class, 'checkFonnteStatus'])->name('configuration.fonnte-status');

        // Log notifikasi WhatsApp
        Route::get('notifikasi', [WaNotificationController::class, 'index'])->name('notifikasi.index');

        // Arsip — nasib akhir (peninjauan retensi) & pemusnahan (approval-based)
        Route::patch('arsip/nasib-akhir/{jenis}/{id}/musnah', [ArsipController::class, 'tetapkanMusnah'])->name('arsip.musnah');
        Route::post('arsip/nasib-akhir/{jenis}/{id}/permanen', [ArsipController::class, 'tetapkanPermanen'])->name('arsip.permanen');
        Route::delete('arsip/nasib-akhir/{jenis}/{id}/approve', [ArsipController::class, 'approveMusnah'])->name('arsip.approve');
        Route::delete('arsip/nasib-akhir/bulk-approve', [ArsipController::class, 'bulkApprove'])->name('arsip.bulk-approve');
    });

    // Nasib Akhir Arsip — agendaris (kelola) & pimpinan (pengawasan, read-only).
    // Semua aksi yang mengubah data (tetapkan permanen/musnah, approve) tetap khusus agendaris
    // di grup role:agendaris di atas. Export CSV/PDF hanya baca, jadi boleh untuk keduanya.
    Route::middleware('role:agendaris,pimpinan')->group(function () {
        Route::get('arsip/nasib-akhir', [ArsipController::class, 'index'])->name('arsip.index');
        Route::get('arsip/export/csv', [ArsipController::class, 'exportCsv'])->name('arsip.export-csv');
        Route::get('arsip/export/pdf', [ArsipController::class, 'exportPdf'])->name('arsip.export-pdf');
    });

    // Daftar surat masuk — semua peran boleh buka, isinya difilter per peran di controller
    // (SuratMasuk::visibleTo): agendaris & pimpinan semua; kabid/sekretariat/staf hanya
    // surat yang didisposisikan kepadanya/bidangnya. CRUD tetap khusus agendaris (grup di atas).
    Route::get('surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');

    // Disposisi — akses per-role dicek di controller (unit tujuan bisa siapapun: kabid/sekretariat/staf)
    Route::get('disposisi', [DisposisiController::class, 'inbox'])->name('disposisi.inbox');
    Route::get('surat-masuk/{suratMasuk}/disposisi', [DisposisiController::class, 'show'])->name('disposisi.show');
    Route::post('surat-masuk/{suratMasuk}/disposisi', [DisposisiController::class, 'store'])->name('disposisi.store');
    Route::post('disposisi/{disposisi}/forward', [DisposisiController::class, 'forward'])->name('disposisi.forward');
    Route::patch('disposisi/{disposisi}/status', [DisposisiController::class, 'updateStatus'])->name('disposisi.status');
    Route::get('tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
    Route::post('disposisi/{disposisi}/tindak-lanjut', [TindakLanjutController::class, 'store'])->name('tindak-lanjut.store');

    // Surat Keluar — input/edit/hapus khusus agendaris (middleware role, sama seperti surat masuk).
    // Daftar boleh dibuka semua peran, isinya difilter di SuratKeluarController@index:
    // agendaris & pimpinan melihat semua (pimpinan read-only); peran lain sementara hanya
    // surat buatannya sendiri (menunggu keputusan instansi soal visibilitas).
    Route::get('surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');

    Route::middleware('role:agendaris')->group(function () {
        Route::get('surat-keluar/next-nomor', [SuratKeluarController::class, 'nextNomor'])->name('surat-keluar.next-nomor');
        Route::resource('surat-keluar', SuratKeluarController::class)->only([
            'create', 'store', 'edit', 'update', 'destroy',
        ]);
    });
});