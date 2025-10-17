<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\EmailLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes API per il sistema di gestione utenti e carrello.
| Tutte le route sono protette da middleware appropriati.
|
*/

// Test route (da rimuovere in produzione)
Route::get('/test-admin', function () {
    $users = \App\Models\User::with('roles')->get();
    $roles = \App\Models\Role::all();
    return response()->json([
        'users' => $users,
        'roles' => $roles,
        'admin_user' => \App\Models\User::where('email', 'admin@passepartout-utenticarrello.test')->with('roles')->first()
    ]);
});

// Routes di autenticazione pubbliche
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Routes protette da autenticazione
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/change-email', [AuthController::class, 'changeEmail']);
    });
});

// Routes prodotti
Route::prefix('products')->group(function () {
    // Routes pubbliche
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);

    // Routes admin (richiedono autenticazione e ruolo admin)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [ProductController::class, 'toggleStatus']);
        Route::patch('/{id}/update-stock', [ProductController::class, 'updateStock']);
    });
});

// Routes carrello (tutte richiedono autenticazione)
Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::get('/count', [CartController::class, 'getItemsCount']);
    Route::get('/total', [CartController::class, 'getTotal']);
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::put('/update', [CartController::class, 'updateQuantity']);
    Route::delete('/remove', [CartController::class, 'removeFromCart']);
    Route::delete('/clear', [CartController::class, 'clearCart']);
    Route::post('/checkout', [CartController::class, 'checkout']);
});

// Routes amministrative (richiedono autenticazione e ruolo admin)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'getDashboardStats']);
    Route::get('/users/stats', [AdminController::class, 'getUserStats']);
    Route::get('/sales/stats', [AdminController::class, 'getSalesStats']);
    Route::get('/users', [AdminController::class, 'getUsers']);
});

// Routes email logs (protette da autenticazione)
Route::middleware(['auth:sanctum'])->prefix('emails')->group(function () {
    Route::get('/', [EmailLogController::class, 'index']);
    Route::get('/stats', [EmailLogController::class, 'stats']);
    Route::get('/{id}', [EmailLogController::class, 'show']);
});

// Route per test API
Route::get('/ping', function () {
    return response()->json([
        'message' => 'API Passepartout Utenti Carrello funzionante!',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString(),
    ]);
});

// Route protetta per testare l'autenticazione
Route::middleware('auth:sanctum')->get('/test-auth', function (Request $request) {
    return response()->json([
        'message' => 'Autenticazione funzionante!',
        'user' => $request->user()->only(['id', 'name', 'email']),
    ]);
});
