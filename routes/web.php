<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Book Management Routes
    Route::resource('books', BookController::class);
    Route::delete('/books/{id}/force', [BookController::class, 'forceDelete'])->name('books.force-delete');

    // Member Management Routes
    Route::resource('members', MemberController::class);
    Route::get('/members/{member}/borrowing-history', [MemberController::class, 'borrowingHistory'])
        ->name('members.borrowing-history');

    // Borrowing Management Routes
    Route::resource('borrowings', BorrowingController::class);
    Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'return'])
        ->name('borrowings.return');
    Route::get('/borrowings/report/all', [BorrowingController::class, 'report'])->name('borrowings.report');

    // Penalty Management Routes
    Route::resource('penalties', PenaltyController::class);
    Route::post('/penalties/{penalty}/pay', [PenaltyController::class, 'markAsPaid'])
        ->name('penalties.mark-paid');
    Route::post('/penalties/{penalty}/waive', [PenaltyController::class, 'waive'])
        ->name('penalties.waive');
});

// Redirect root to dashboard if authenticated
Route::redirect('/', '/dashboard');