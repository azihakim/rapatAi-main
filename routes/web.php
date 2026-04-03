<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KetersediaanPribadiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotulensiController;
use App\Http\Controllers\PengajuanLogistikController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapatController;
use App\Http\Controllers\TindakLanjutRapatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth'])->group(function () {


    // Tindak Lanjut Rapat (akses: pimpinan & anggota)    
    Route::resource('tindak-lanjut-rapat', TindakLanjutRapatController::class);

     Route::post('/rapat/jadwal-peserta', [RapatController::class, 'jadwalPeserta'])->name('rapat.jadwal-peserta');

    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/rapat', [DashboardController::class, 'store'])->name('dashboard.rapat.store');
    Route::put('/dashboard/rapat/{id}', [DashboardController::class, 'update'])->name('dashboard.rapat.update');
    Route::delete('/dashboard/rapat/{id}', [DashboardController::class, 'destroy'])->name('dashboard.rapat.destroy');

    // Rapat
    Route::get('/rapat', [RapatController::class, 'index'])->name('rapat.index');
    Route::get('/rapat/{id}', [RapatController::class, 'show'])->name('rapat.show');
    Route::put('/rapat/{id}', [RapatController::class, 'update'])->name('rapat.update');
    Route::post('/rapat', [RapatController::class, 'store'])->name('rapat.store');
    Route::post('/rekomendasi-jadwal', [RapatController::class, 'rekomendasiJadwal'])->name('rapat.rekomendasi');
    Route::delete('/rapat/{id}/delete', [RapatController::class, 'destroy'])->name('rapat.destroy');

    // Ketersediaan Pribadi
    Route::get('/ketersediaan-pribadi', [KetersediaanPribadiController::class, 'index'])->name('ketersediaan.index');
    Route::get('/ketersediaan-pribadi/{id}', [KetersediaanPribadiController::class, 'show'])->name('ketersediaan.show');
    Route::put('/ketersediaan-pribadi/{id}', [KetersediaanPribadiController::class, 'update'])->name('ketersediaan.update');
    Route::post('/ketersediaan-pribadi', [KetersediaanPribadiController::class, 'store'])->name('ketersediaan.store');
    Route::delete('/ketersediaan-pribadi/{id}/delete', [KetersediaanPribadiController::class, 'destroy'])->name('ketersediaan.destroy');

    // Logistik
    Route::get('/logistik', [PengajuanLogistikController::class, 'index'])->name('logistik.index');
    Route::get('/logistik/{id}', [PengajuanLogistikController::class, 'show'])->name('logistik.show');
    Route::put('/logistik/{id}', [PengajuanLogistikController::class, 'update'])->name('logistik.update');
    Route::post('/logistik', [PengajuanLogistikController::class, 'store'])->name('logistik.store');
    Route::delete('/logistik/{id}/delete', [PengajuanLogistikController::class, 'destroy'])->name('logistik.destroy');

    // notulensi
    Route::get('/notulensi', [NotulensiController::class, 'index'])->name('notulensi.index');
    Route::get('/notulensi/{id}', [NotulensiController::class, 'show'])->name('notulensi.show');
    Route::put('/notulensi/{id}', [NotulensiController::class, 'update'])->name('notulensi.update');
    Route::post('/notulensi', [NotulensiController::class, 'store'])->name('notulensi.store');
    Route::post('/notulensi/speech-to-text', [NotulensiController::class, 'generateAudioToText'])->name('notulensi.speechToText');
    Route::delete('/notulensi/{id}/delete', [NotulensiController::class, 'destroy'])->name('notulensi.destroy');

    // Approval Rapat
    Route::get('/approval/approve-rapat', [ApprovalController::class, 'approveRapatView'])->name('approval.approve-rapat');
    Route::get('/approval/surat-undangan-rapat/{id}', [ApprovalController::class, 'viewSuratUndanganRapat'])->name('approval.surat-undangan-rapat');
    Route::post('/approval/update-status/{id}', [ApprovalController::class, 'updateStatus'])->name('approval.update-status');

    // Approval Notulen
    Route::get('/approval/approve-notulen', [ApprovalController::class, 'viewApproveNotulen'])->name('approval.approve-notulen');
    Route::get('/approval/notulen/{id}', [ApprovalController::class, 'detailViewNotulen'])->name('approval.view-notulen');
    Route::post('/approval/update-status-notulen/{id}', [ApprovalController::class, 'updateStatusNotulen'])->name('approval.update-status-notulen');

    // Approval Logistik
    Route::get('/approval/approve-logistik', [ApprovalController::class, 'viewApprovalLogistik'])->name('approval.approve-logistik');
    Route::post('/approval/update-status-logistik/{id}', [ApprovalController::class, 'updateStatusLogistik'])->name('approval.update-status-logistik');

    // Notification
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');





    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
