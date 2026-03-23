<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PrinterLocationController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ConsumableTypeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ConsumableRequestController;
use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Standard User Requests
    Route::resource('requests', ConsumableRequestController::class)->only(['index', 'create', 'store']);

    // AJAX API
    Route::get('/api/departments/{department}/locations', [ConsumableRequestController::class, 'getLocationsByDepartment']);
    Route::get('/api/departments/{department}/locations/{location}/printers', [ConsumableRequestController::class, 'getPrintersByLocation']);
    Route::get('/api/printers/{printer}/inventory', [ConsumableRequestController::class, 'getInventory']);

    // Asset Management (We use 'types' here, but I will map 'consumable-types' too just in case your nav menu looks for it!)
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit', 'update']);
    Route::resource('locations', PrinterLocationController::class);
    Route::resource('printers', PrinterController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('types', ConsumableTypeController::class)->names('types');
    Route::resource('consumable-types', ConsumableTypeController::class)->names('consumable-types'); // Backup fallback
    Route::resource('inventory', InventoryController::class);

    // Operations / Inventory Management
    Route::middleware('can:manage-inventory')->group(function () {
        Route::get('/admin/requests', [AdminRequestController::class, 'index'])->name('admin.requests.index');
        Route::patch('/admin/request-items/{requestItem}', [AdminRequestController::class, 'updateItem'])->name('admin.request-items.update');

        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        // Route::get('/admin/reports/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.pdf');
        Route::get('/admin/reports/print', [ReportController::class, 'printReport'])->name('admin.reports.print');
        Route::get('/admin/reports/excel', [ReportController::class, 'exportExcel'])->name('admin.reports.excel');
    });

    // System Administration
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('admin.logs.index');
    });

    Route::middleware(['auth', 'can:manage-printers'])->group(function () {
        Route::resource('printers', PrinterController::class);
    });

    // FULFILLMENT DASHBOARD ROUTES
    Route::middleware(['auth', 'can:manage-requests'])->group(function () {
        Route::get('/admin/requests', [\App\Http\Controllers\AdminRequestController::class, 'index'])->name('admin.requests.index');
        Route::get('/admin/requests/{request}', [\App\Http\Controllers\AdminRequestController::class, 'show'])->name('admin.requests.show');
        // Add any update/approve routes here too

        Route::put('/admin/requests/{request}', [\App\Http\Controllers\AdminRequestController::class, 'update'])->name('admin.requests.update');

        Route::post('/admin/requests/{request}/recall/{item}', [\App\Http\Controllers\AdminRequestController::class, 'recall'])->name('admin.requests.recall');
    });
});

require __DIR__ . '/auth.php';
