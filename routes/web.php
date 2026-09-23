<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\Crm\ActionController;
use App\Http\Controllers\Crm\BranchController;
use App\Http\Controllers\Crm\DashboardController as CrmDashboardController;
use App\Http\Controllers\Crm\FeedbackController;
use App\Http\Controllers\Crm\GuestController;
use App\Http\Controllers\Crm\InteractionController;
use App\Http\Controllers\Crm\PromotionController;
use App\Http\Controllers\Crm\ReservationController;
use App\Http\Controllers\Crm\SaleController;
use App\Http\Controllers\Crm\SalesReportController;
use App\Http\Controllers\Crm\TenantSwitchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\SubscriptionController;
use App\Http\Controllers\Master\TenantController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PenaltyController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// InnEase CRM
Route::middleware('auth')->group(function () {
    Route::get('/', [CrmDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [CrmDashboardController::class, 'index'])->name('crm.dashboard');

    // Tenant / branch context switching
    Route::post('/context/tenant', [TenantSwitchController::class, 'switchTenant'])->name('context.tenant');
    Route::post('/context/branch', [TenantSwitchController::class, 'switchBranch'])->name('context.branch');

    // Guests, reservations and front-desk transactions
    Route::resource('guests', GuestController::class);
    Route::resource('reservations', ReservationController::class);
    Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');

    // Data collection
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('/feedback/{feedback}/resolve', [FeedbackController::class, 'resolve'])->name('feedback.resolve');
    Route::post('/feedback/{feedback}/escalate', [FeedbackController::class, 'escalate'])->name('feedback.escalate');

    Route::resource('interactions', InteractionController::class)->except('show');

    // Sales
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');

    // Actions
    Route::resource('actions', ActionController::class)->except('show');
    Route::post('/actions/{action}/status', [ActionController::class, 'updateStatus'])->name('actions.status');

    // Promotions
    Route::resource('promotions', PromotionController::class);
    Route::post('/promotions/{promotion}/approve', [PromotionController::class, 'approve'])
        ->middleware('role:master,admin,manager')->name('promotions.approve');
    Route::post('/promotions/{promotion}/reject', [PromotionController::class, 'reject'])
        ->middleware('role:master,admin,manager')->name('promotions.reject');

    // Multi-branch management (tenants with branching enabled)
    Route::resource('branches', BranchController::class)->except('show');

    // Master / super admin
    Route::middleware('role:master')->prefix('master')->name('master.')->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::post('/tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
        Route::resource('subscriptions', SubscriptionController::class)->except('show');
    });

    // Legacy library management module
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
    Route::resource('books', BookController::class);
    Route::delete('/books/{id}/force', [BookController::class, 'forceDelete'])->name('books.force-delete');
    Route::resource('members', MemberController::class);
    Route::get('/members/{member}/borrowing-history', [MemberController::class, 'borrowingHistory'])
        ->name('members.borrowing-history');
    Route::resource('borrowings', BorrowingController::class);
    Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'return'])
        ->name('borrowings.return');
    Route::get('/borrowings/report/all', [BorrowingController::class, 'report'])->name('borrowings.report');
    Route::resource('penalties', PenaltyController::class);
    Route::post('/penalties/{penalty}/pay', [PenaltyController::class, 'markAsPaid'])
        ->name('penalties.mark-paid');
    Route::post('/penalties/{penalty}/waive', [PenaltyController::class, 'waive'])
        ->name('penalties.waive');
});
