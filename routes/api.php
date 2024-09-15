<?php

use App\Http\Controllers\BankPaymentController;
use App\Http\Controllers\CardPaymentController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
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

Route::middleware(['check.apikey'])->group(function () {
    
    Route::get('/student-payment-history', [BankPaymentController::class, 'studentHistoryPayment']);
    Route::post('/student-bank-payment', [BankPaymentController::class, 'studentClassFeesBankPayment']);
    Route::get('/student-pending-payment', [BankPaymentController::class, 'studentPendingPayment']);
    Route::post('/student-payment', [BankPaymentController::class, 'studentPayment']);


    Route::get('/student-payment-history/Bank', [BankPaymentController::class, 'studentBankPayment']);
    Route::get('/student-payment-history/Card', [BankPaymentController::class, 'studentBankPayment']);

    Route::post('/student-card-payment', [CardPaymentController::class, 'initPayment'])->name('payment.init');
    Route::get('/payment-history-search', [PaymentController::class, 'HistoryPaymentSearch']);



   
}); 

