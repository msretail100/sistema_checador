<?php

use App\Enums\UserRole;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:root,admin,worker'])->get('/dashboard', function () {
    return match (auth()->user()->role) {
        UserRole::Root->value => redirect()->route('root.dashboard'),
        UserRole::Admin->value => redirect()->route('admin.dashboard'),
        UserRole::Worker->value => redirect()->route('worker.dashboard'),
        default => abort(403),
    };
})->name('dashboard');

Route::middleware(['auth', 'role:root'])->prefix('root')->name('root.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'root'])->name('dashboard');
    Route::get('/reportes/{period}', [AttendanceReportController::class, 'index'])
        ->whereIn('period', ['daily', 'monthly', 'yearly'])
        ->name('reports');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/reportes/{period}', [AttendanceReportController::class, 'index'])
        ->whereIn('period', ['daily', 'monthly'])
        ->name('reports');
});

Route::middleware(['auth', 'role:worker'])->group(function (): void {
    Route::get('/trabajador', [DashboardController::class, 'worker'])->name('worker.dashboard');
    Route::post('/asistencia', [AttendanceController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('attendance.store');
});

Route::middleware(['auth', 'role:root,admin'])->prefix('usuarios')->name('users.')->group(function (): void {
    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::get('/crear', [UserManagementController::class, 'create'])->name('create');
    Route::post('/', [UserManagementController::class, 'store'])->name('store');
    Route::patch('/{user}/estado', [UserManagementController::class, 'toggle'])->name('toggle');
    Route::middleware('role:root')->group(function (): void {
        Route::get('/{user}/editar', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
    });
});
