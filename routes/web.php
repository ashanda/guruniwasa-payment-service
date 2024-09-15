<?php

use App\Http\Controllers\CardPaymentController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/payment/init', [CardPaymentController::class, 'initPayment'])->name('payment.init');
Route::get('/payment/complete', [CardPaymentController::class, 'completePayment'])->name('payment.complete');