<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HistoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
    Route::post('/marketplace', [MarketplaceController::class, 'store'])->name('marketplace.store');
    Route::post('/marketplace/stock/{id}', [MarketplaceController::class, 'updateStock'])->name('marketplace.stock');
    Route::delete('/marketplace/{id}', [MarketplaceController::class, 'destroy'])->name('marketplace.destroy');
    Route::view('/forum', 'forum')->name('forum');
    Route::view('/sensor', 'sensor')->name('sensor');
    Route::view('/robot', 'robot')->name('robot');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/forum', [ForumController::class, 'index'])->name('forum');
    Route::post('/forum/post', [ForumController::class, 'storePost'])->name('forum.post');
    Route::post('/forum/comment/{postId}', [ForumController::class, 'storeComment'])->name('forum.comment');
    Route::post('/forum/like/{postId}', [ForumController::class, 'toggleLike'])->name('forum.like');
    Route::delete('/forum/delete/{postId}', [ForumController::class, 'deletePost'])->name('forum.delete');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::post('/history/detection', [HistoryController::class, 'storeDetection'])->name('history.store_detection');
    Route::post('/history/click', [HistoryController::class, 'trackClick'])->name('history.track_click');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
    Route::get('/api/product-recommendation', [App\Http\Controllers\MarketplaceController::class, 'getRecommendation']);
    Route::get('/marketplace/{id}/edit', [MarketplaceController::class, 'edit'])->name('marketplace.edit');
    Route::put('/marketplace/{id}', [MarketplaceController::class, 'update'])->name('marketplace.update');
});

// GROUP TEKNISI (MANAJEMEN USER)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.users');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('admin.users.delete');
});

// GROUP LAPORAN (PETANI & TEKNISI)
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
    Route::put('/laporan/{id}', [App\Http\Controllers\ReportController::class, 'reply'])->name('reports.reply');
});

Route::middleware(['auth'])->group(function () {
    // Halaman Utama
    Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    
    // Buat Laporan Baru
    Route::post('/laporan', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
    
    // --- JALUR KHUSUS CHAT (Biar Gak Bentrok) ---
    
    // 1. Kirim Chat (POST) -> Perhatikan URL-nya ada kata 'chat'
    Route::post('/chat/kirim/{id}', [App\Http\Controllers\ReportController::class, 'reply'])->name('chat.kirim');
    
    // 2. Selesaikan Masalah (PUT)
    Route::put('/chat/selesai/{id}', [App\Http\Controllers\ReportController::class, 'resolve'])->name('chat.selesai');
});

require __DIR__.'/auth.php';
