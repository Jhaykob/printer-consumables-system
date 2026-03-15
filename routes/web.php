<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrinterLocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ConsumableTypeController;
use App\Http\Controllers\ConsumableRequestController;
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

// Public landing page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (Requires user to be logged in and verified)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// All authenticated routes
Route::middleware('auth')->group(function () {

    // --- Standard Profile Routes (Laravel Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // --- User & Permission Management ---
    // Protected by the 'manage-users' Gate defined in AppServiceProvider
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });


    // --- Asset Management: Printer Locations ---
    // Anyone logged in can view the locations index.
    // The controller protects store/update/destroy using Gate::authorize('manage-assets').
    // The Blade view uses @can('manage-assets') to hide the Add/Delete buttons.
    Route::resource('locations', PrinterLocationController::class)->except(['show', 'create', 'edit']);

    Route::resource('printers', PrinterController::class)->except(['show', 'create', 'edit']);

    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit', 'update']);
    Route::resource('colors', ColorController::class)->except(['show', 'create', 'edit', 'update']);

    Route::resource('consumable-types', ConsumableTypeController::class)->except(['show', 'create', 'edit', 'update']);

    Route::resource('inventory', InventoryController::class)->except(['show', 'create', 'edit', 'update']);

    // Standard users can access these without special permissions
    Route::resource('requests', ConsumableRequestController::class)->only(['index', 'create', 'store']);
});

// Load the default authentication routes (Login, Register, Password Reset, etc.)
require __DIR__ . '/auth.php';
