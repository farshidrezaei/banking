<?php

use App\Http\Controllers\API\V1\Card\BankingController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->name('v1.')->group(function () {
    Route::prefix('banking')->name('banking.')->controller(BankingController::class)->group(function () {
        Route::post('card-to-card', 'cardToCard')->name('card-to-card');
        Route::get('top-users', 'getTopUsers')->name('top-users');
    });
});
