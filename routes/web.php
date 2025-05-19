<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas protegidas (requer autenticação) - SEM o middleware log.activity
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TransactionController::class, 'dashboard'])->name('dashboard');

    Route::get('/deposit', [TransactionController::class, 'showDepositForm'])->name('deposit.form');
    Route::post('/deposit', [TransactionController::class, 'deposit'])->name('deposit.store');

    Route::get('/transfer', [TransactionController::class, 'showTransferForm'])->name('transfer.form');
    Route::post('/transfer', [TransactionController::class, 'transfer'])->name('transfer.store');

    Route::get('/transactions', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

    Route::post('/transactions/{id}/reverse', [TransactionController::class, 'reverse'])->name('transactions.reverse');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
