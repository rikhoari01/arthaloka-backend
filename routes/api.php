<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AtmController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route for Login
Route::post('auth/login', [AuthController::class, 'login']);

// Route for Transaction
Route::post('transaction', [TransactionController::class, 'store'])->name('transaction.store');

// Route get atm list
Route::get('atm/list', [AtmController::class, 'list'])->name('atm.list');

Route::group(['middleware' => 'jwt.verify'], function () {
    // Authentication
    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('me', [AuthController::class, 'me'])->name('profile');
    });

    // Route for Customer
    Route::resource('customer', CustomerController::class);

    // Route for ATM
    Route::resource('atm', AtmController::class);

    // Route for history
    Route::get('history', [TransactionController::class, 'index'])->name('transaction.index');
});