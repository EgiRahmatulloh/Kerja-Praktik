<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DataItemController;
use App\Http\Controllers\Admin\FilledLetterController;
use App\Http\Controllers\Admin\LetterQueueController;
use App\Http\Controllers\Admin\LetterTypeController;
use App\Http\Controllers\Admin\ServiceScheduleController;
use App\Http\Controllers\Admin\TemplateSuratController;
use App\Http\Controllers\Admin\AnnouncementController; // Tambahkan import ini
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\LetterController;
use App\Http\Controllers\User\LetterQueueController as UserLetterQueueController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rute untuk autentikasi
Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk registrasi
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// Rute untuk admin
// Hapus baris ini: use App\Http\Controllers\Admin\NotificationController;

// Di dalam grup route admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Template Surat
    Route::resource('templates', TemplateSuratController::class);

    // Data Item (Variabel)
    Route::resource('data-items', DataItemController::class);

    // Jenis Surat
    Route::resource('letter-types', LetterTypeController::class);

    // Surat yang Diisi
    Route::resource('filled-letters', FilledLetterController::class)->except(['create', 'store', 'destroy']);
    Route::get('filled-letters/{id}/print', [FilledLetterController::class, 'print'])->name('filled-letters.print');
    Route::get('filled-letters/{id}/pdf', [FilledLetterController::class, 'generatePdf'])->name('filled-letters.pdf');
    Route::put('filled-letters/{id}/status', [FilledLetterController::class, 'updateStatus'])->name('filled-letters.update-status');
    Route::put('filled-letters/{id}/template', [FilledLetterController::class, 'updateTemplate'])->name('filled-letters.update-template');

    // Antrian Surat
    Route::resource('letter-queues', LetterQueueController::class)->except(['create', 'store', 'destroy']);
    Route::put('letter-queues/{id}/status', [LetterQueueController::class, 'updateStatus'])->name('letter-queues.update-status');

    // Jadwal Pelayanan
    Route::resource('service-schedules', ServiceScheduleController::class);
    Route::post('service-schedules/{id}/pause', [ServiceScheduleController::class, 'pause'])->name('service-schedules.pause');
    Route::post('service-schedules/{id}/unpause', [ServiceScheduleController::class, 'unpause'])->name('service-schedules.unpause');

    // Pengumuman
    Route::resource('announcements', AnnouncementController::class);

    // Notifikasi
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Rute untuk user
Route::prefix('user')->name('user.')->middleware(['auth', 'user'])->group(function () {
    // Dashboard
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');

    // Surat
    Route::get('/letters', [LetterController::class, 'index'])->name('letters.index');
    Route::get('/letters/create/{id}', [LetterController::class, 'create'])->name('letters.create');
    Route::post('/letters/store/{id}', [LetterController::class, 'store'])->name('letters.store');
    Route::get('/letters/history', [LetterController::class, 'history'])->name('letters.history');
    Route::get('/letters/{id}', [LetterController::class, 'show'])->name('letters.show');
    Route::get('/letters/{id}/edit', [LetterController::class, 'edit'])->name('letters.edit');
    Route::put('/letters/{id}', [LetterController::class, 'update'])->name('letters.update');
    Route::get('/letters/{id}/download', [LetterController::class, 'download'])->name('letters.download');

    // Antrian Surat
    Route::get('/letter-queues', [UserLetterQueueController::class, 'index'])->name('letter-queues.index');
    Route::get('/letter-queues/{id}', [UserLetterQueueController::class, 'show'])->name('letter-queues.show');
    // Notifikasi
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});
