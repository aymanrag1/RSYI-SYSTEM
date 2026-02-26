<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\Warehouse\ProductController;
use App\Http\Controllers\Students\StudentController;

// ─── Authentication ───────────────────────────────────────────────────────────

Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout')->middleware('rsyi.auth');

// ─── Protected Routes ─────────────────────────────────────────────────────────

Route::middleware('rsyi.auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── HR System ─────────────────────────────────────────────────────────
    Route::prefix('hr')->name('hr.')->group(function () {

        Route::get('employees',                    [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/create',             [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees',                   [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('employees/{employee}',         [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('employees/{employee}/edit',    [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}',         [EmployeeController::class, 'update'])->name('employees.update');

        Route::get('leaves',                       [LeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/create',                [LeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves',                      [LeaveController::class, 'store'])->name('leaves.store');
        Route::post('leaves/{leave}/approve',      [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject',       [LeaveController::class, 'reject'])->name('leaves.reject');
    });

    // ── Warehouse ──────────────────────────────────────────────────────────
    Route::prefix('warehouse')->name('warehouse.')->group(function () {

        Route::get('products',                     [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create',              [ProductController::class, 'create'])->name('products.create');
        Route::post('products',                    [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit',      [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}',           [ProductController::class, 'update'])->name('products.update');
    });

    // ── Student Affairs ────────────────────────────────────────────────────
    Route::prefix('students')->name('students.')->group(function () {

        Route::get('/',                            [StudentController::class, 'index'])->name('index');
        Route::get('create',                       [StudentController::class, 'create'])->name('create');
        Route::post('/',                           [StudentController::class, 'store'])->name('store');
        Route::get('{student}',                    [StudentController::class, 'show'])->name('show');
        Route::get('{student}/edit',               [StudentController::class, 'edit'])->name('edit');
        Route::put('{student}',                    [StudentController::class, 'update'])->name('update');
    });
});
