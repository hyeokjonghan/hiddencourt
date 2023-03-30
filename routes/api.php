<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\CCTV\CCTVController;
use App\Http\Controllers\ReservationController;

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
Route::prefix('user')->group(function() {

    // 일반 로그인.. SNS 관련 처리 할 때 추가 편집 해줘야 함
    Route::post('/login', [ApiAuthController::class, 'createToken']);
    Route::post('/token/refersh', [ApiAuthController::class, 'tokenRefresh']);

    // 일반 회원가입
    Route::post('/register', [ApiAuthController::class, 'createUser']);
});

// 로그인 하고 사용 할 수 있는 API
Route::middleware('auth:api')->group(function() {
    // 관리자만 사용 할 수 있는 API
    Route::post('health', [ApiAuthController::class, 'loginHealthCheck']);
    Route::post('authTest', [CCTVController::class, 'getAuthToken']);
    Route::post('account', [CCTVController::class, 'accountInfo']);
    Route::post('camera', [CCTVController::class, 'camera']);
    Route::post('recodeVideo', [CCTVController::class, 'recodeVideo']);
    Route::post('recordVideoList', [CCTVController::class, 'recordVideoList']);

    

    Route::prefix('reservation')->group(function() {
        Route::get('/list', [ReservationController::class, 'reservationList']);
        Route::post('/append', [ReservationController::class, 'appendReservation']);
        Route::put('/modify/{reservation}', [ReservationController::class, 'modifyReservation']);
        Route::put('/state/{reservation}', [ReservationController::class, 'modifyReservationState']);
        Route::delete('/delete/{reservation}', [ReservationController::class, 'deleteReservation']);
    });
});