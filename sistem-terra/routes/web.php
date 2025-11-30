<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HistoryController;
use App\Services\FirebaseService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'Terra',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-firebase', function() {
    try {
        $firebase = new FirebaseService();
        $result = $firebase->saveDetection('test_user', [
            'label' => 'test',
            'dominan_disease' => 'test_disease',
            'confidence' => 0.95,
            'dominan_confidence_avg' => 0.95,
            'status' => 'sehat'
        ]);
        return response()->json(['status' => 'success', 'data' => $result]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
    Route::post('/marketplace', [MarketplaceController::class, 'store'])->name('marketplace.store');
    Route::get('/marketplace/{id}/edit', [MarketplaceController::class, 'edit'])->name('marketplace.edit');
    Route::put('/marketplace/{id}', [MarketplaceController::class, 'update'])->name('marketplace.update');
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
    Route::get('/history/refresh', [HistoryController::class, 'refresh'])->name('history.refresh');
    Route::get('/api/detections', [HistoryController::class, 'getDetections'])->name('api.detections');
    Route::post('/history/click', [HistoryController::class, 'trackClick'])->name('history.track_click');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
    Route::delete('/history/{id}', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::get('/api/product-recommendation', [App\Http\Controllers\MarketplaceController::class, 'getRecommendation']);
    });


// Sensor API Routes (no auth required)
Route::get('/api/sensor/current', [App\Http\Controllers\SensorApiController::class, 'getCurrent'])->name('api.sensor.current');
Route::get('/api/sensor/history', [App\Http\Controllers\SensorApiController::class, 'getHistory']);
Route::post('/api/sensor/generate', [App\Http\Controllers\SensorApiController::class, 'generateAndSave'])->name('api.sensor.generate')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/api/sensor/firebase', [App\Http\Controllers\SensorApiController::class, 'getFromFirebase']);
    
// Sensor untuk Detections
Route::post('/api/sensor/auto-update-detections', [App\Http\Controllers\SensorApiController::class, 'autoUpdateDetections'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Tooltip API Routes
Route::get('/api/tooltip', [App\Http\Controllers\TooltipApiController::class, 'getTooltips'])->name('api.tooltip');

// GROUP TEKNISI (MANAJEMEN USER)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.users');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('admin.users.delete');
});

// GROUP LAPORAN (PETANI & TEKNISI)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
    Route::put('/laporan/{id}', [App\Http\Controllers\ReportController::class, 'reply'])->name('reports.reply');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
    
    // --- JALUR KHUSUS CHAT (Biar Gak Bentrok) ---
    
    // 1. Kirim Chat (POST) -> Perhatikan URL-nya ada kata 'chat'
    Route::post('/chat/kirim/{id}', [App\Http\Controllers\ReportController::class, 'reply'])->name('chat.kirim');
    
    // 2. Selesaikan Masalah (PUT)
    Route::put('/chat/selesai/{id}', [App\Http\Controllers\ReportController::class, 'resolve'])->name('chat.selesai');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

require __DIR__.'/auth.php';
