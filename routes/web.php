<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ms\OneDriveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web'])->group(function() {
    Route::prefix('ms')->group(function() {
        Route::get('login',[OneDriveController::class, 'getOauth']);
        Route::get('redirect',[OneDriveController::class,'redirect']);
    });
    
});



Route::get('/', function () {
    return view('welcome');
});
