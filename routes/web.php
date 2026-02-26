<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\Warehouse\ProductController;
use App\Http\Controllers\Warehouse\AddOrderController;
use App\Http\Controllers\Warehouse\WithdrawalOrderController;
use App\Http\Controllers\Warehouse\PurchaseRequestController;
use App\Http\Controllers\Warehouse\SupplierController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\CohortController;
use App\Http\Controllers\Students\PermitController;
use App\Http\Controllers\Students\BehaviorController;

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

        // Employees
        Route::get('employees',                    [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/create',             [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees',                   [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('employees/{employee}',         [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('employees/{employee}/edit',    [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}',         [EmployeeController::class, 'update'])->name('employees.update');

        // Leaves
        Route::get('leaves',                       [LeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/create',                [LeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves',                      [LeaveController::class, 'store'])->name('leaves.store');
        Route::post('leaves/{leave}/approve',      [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject',       [LeaveController::class, 'reject'])->name('leaves.reject');

        // Attendance
        Route::get('attendance',                   [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance',                  [AttendanceController::class, 'store'])->name('attendance.store');

        // Departments
        Route::get('departments',                  [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/create',           [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments',                 [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('departments/{department}/edit',[DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}',     [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{department}',  [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    // ── Warehouse ──────────────────────────────────────────────────────────
    Route::prefix('warehouse')->name('warehouse.')->group(function () {

        // Products
        Route::get('products',                            [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create',                     [ProductController::class, 'create'])->name('products.create');
        Route::post('products',                           [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit',             [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}',                  [ProductController::class, 'update'])->name('products.update');

        // Add Orders (إضافة مخزون)
        Route::get('add-orders',                          [AddOrderController::class, 'index'])->name('add-orders.index');
        Route::get('add-orders/create',                   [AddOrderController::class, 'create'])->name('add-orders.create');
        Route::post('add-orders',                         [AddOrderController::class, 'store'])->name('add-orders.store');

        // Withdrawal Orders (أوامر صرف)
        Route::get('withdrawal-orders',                   [WithdrawalOrderController::class, 'index'])->name('withdrawal-orders.index');
        Route::get('withdrawal-orders/create',            [WithdrawalOrderController::class, 'create'])->name('withdrawal-orders.create');
        Route::post('withdrawal-orders',                  [WithdrawalOrderController::class, 'store'])->name('withdrawal-orders.store');

        // Purchase Requests (طلبات شراء)
        Route::get('purchase-requests',                   [PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
        Route::get('purchase-requests/create',            [PurchaseRequestController::class, 'create'])->name('purchase-requests.create');
        Route::post('purchase-requests',                  [PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
        Route::post('purchase-requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
        Route::post('purchase-requests/{purchaseRequest}/reject',  [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');

        // Suppliers (الموردون)
        Route::get('suppliers',                           [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/create',                    [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers',                          [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('suppliers/{supplier}/edit',           [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('suppliers/{supplier}',                [SupplierController::class, 'update'])->name('suppliers.update');
    });

    // ── Student Affairs ────────────────────────────────────────────────────
    Route::prefix('students')->name('students.')->group(function () {

        // Students
        Route::get('/',                            [StudentController::class, 'index'])->name('index');
        Route::get('create',                       [StudentController::class, 'create'])->name('create');
        Route::post('/',                           [StudentController::class, 'store'])->name('store');
        Route::get('show/{student}',               [StudentController::class, 'show'])->name('show');
        Route::get('{student}/edit',               [StudentController::class, 'edit'])->name('edit');
        Route::put('{student}',                    [StudentController::class, 'update'])->name('update');

        // Cohorts (الدفعات)
        Route::get('cohorts',                      [CohortController::class, 'index'])->name('cohorts.index');
        Route::get('cohorts/create',               [CohortController::class, 'create'])->name('cohorts.create');
        Route::post('cohorts',                     [CohortController::class, 'store'])->name('cohorts.store');
        Route::get('cohorts/{cohort}/edit',        [CohortController::class, 'edit'])->name('cohorts.edit');
        Route::put('cohorts/{cohort}',             [CohortController::class, 'update'])->name('cohorts.update');

        // Exit Permits (تصاريح الخروج)
        Route::get('exit-permits',                         [PermitController::class, 'exitIndex'])->name('exit-permits.index');
        Route::get('exit-permits/create',                  [PermitController::class, 'exitCreate'])->name('exit-permits.create');
        Route::post('exit-permits',                        [PermitController::class, 'exitStore'])->name('exit-permits.store');
        Route::post('exit-permits/{exitPermit}/approve',   [PermitController::class, 'exitApprove'])->name('exit-permits.approve');
        Route::post('exit-permits/{exitPermit}/reject',    [PermitController::class, 'exitReject'])->name('exit-permits.reject');

        // Overnight Permits (تصاريح المبيت)
        Route::get('overnight-permits',                          [PermitController::class, 'overnightIndex'])->name('overnight-permits.index');
        Route::get('overnight-permits/create',                   [PermitController::class, 'overnightCreate'])->name('overnight-permits.create');
        Route::post('overnight-permits',                         [PermitController::class, 'overnightStore'])->name('overnight-permits.store');
        Route::post('overnight-permits/{overnightPermit}/approve',[PermitController::class, 'overnightApprove'])->name('overnight-permits.approve');
        Route::post('overnight-permits/{overnightPermit}/reject', [PermitController::class, 'overnightReject'])->name('overnight-permits.reject');

        // Behavior (السلوك والمخالفات)
        Route::get('behavior',                     [BehaviorController::class, 'index'])->name('behavior.index');
        Route::get('behavior/create',              [BehaviorController::class, 'create'])->name('behavior.create');
        Route::post('behavior',                    [BehaviorController::class, 'store'])->name('behavior.store');
    });
});
