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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // --- BREEZE PROFILE ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- STANDARD USER REQUESTS (Everyone can access) ---
    Route::resource('requests', ConsumableRequestController::class)->only(['index', 'create', 'store']);

    // --- AJAX CASCADING DROPDOWNS (For the Request Form) ---
    Route::get('/api/departments/{department}/locations', [ConsumableRequestController::class, 'getLocationsByDepartment']);
    Route::get('/api/departments/{department}/locations/{location}/printers', [ConsumableRequestController::class, 'getPrintersByLocation']);
    Route::get('/api/printers/{printer}/inventory', [ConsumableRequestController::class, 'getInventory']);


    /*
    |----------------------------------------------------------------------
    | Administrator Routes (Protected by Gates inside Controllers)
    |----------------------------------------------------------------------
    */

    // --- ASSET MANAGEMENT (Gate: manage-assets) ---
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit', 'update']);
    Route::resource('locations', PrinterLocationController::class);
    Route::resource('printers', PrinterController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('types', ConsumableTypeController::class);
    Route::resource('inventory', InventoryController::class);


    // --- INVENTORY FULFILLMENT & REPORTS (Gate: manage-inventory) ---
    Route::middleware('can:manage-inventory')->group(function () {
        Route::get('/admin/requests', [AdminRequestController::class, 'index'])->name('admin.requests.index');
        Route::patch('/admin/request-items/{requestItem}', [AdminRequestController::class, 'updateItem'])->name('admin.request-items.update');

        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    });


    // --- USER MANAGEMENT & AUDIT LOGS (Gate: manage-users) ---
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class);

        Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('admin.logs.index');
    });
});

require __DIR__ . '/auth.php';
