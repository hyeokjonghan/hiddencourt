<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\CCTV\CCTVController;
use App\Http\Controllers\HiddenCourt\CartController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\HiddenCourt\ClipController;
use App\Http\Controllers\ktApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('upload')->group(function() {
    Route::post('/{uploadDivision}',[UploadController::class,'fileUpload']);
});

// Route::get('/connect/test', [CartController::class, 'getTodayReservation']);



Route::prefix('user')->group(function() {
    Route::post('/login', [ApiAuthController::class, 'createToken']);
    // Route::post('/token/refersh', [ApiAuthController::class, 'tokenRefresh']);
    Route::post('/register', [ApiAuthController::class, 'createUser']);
});


Route::prefix('cctv')->group(function() {
    // Route::get('/camera', [CameraController::class, 'insertCameraInit']);
    // Route::get('/connect/test', [ClipController::class, 'setClipToday']);
    Route::get('/connect/test', [ktApiController::class, 'getAuthToken']);
    Route::post('/cart/sync',[ClipController::class, 'clipSync']);
    Route::get('/cart/{cartId}/{cartTime}',[CCTVController::class, 'getCart']);
});

// 로그인 하고 사용 할 수 있는 API
Route::middleware('auth:api')->group(function() {
    Route::post('health', [ApiAuthController::class, 'loginHealthCheck']);
    Route::post('authTest', [CCTVController::class, 'getAuthToken']);
    Route::post('account', [CCTVController::class, 'accountInfo']);
    Route::post('camera', [CCTVController::class, 'camera']);
    Route::post('recordVideo', [CCTVController::class, 'recordVideo']);
    Route::post('recordVideoList', [CCTVController::class, 'recordVideoList']);

    Route::post('/test/camera', [CameraController::class, 'insertCameraInit']);
});
