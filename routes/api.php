<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\IngresoController;

// Preflight CORS
Route::options('{any}', function () {
    return response()->json('OK', 200);
})->where('any', '.*');

Route::prefix('v1')->group(function () {

    // Rutas públicas
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);

        Route::apiResource('clientes',  ClienteController::class);
        Route::apiResource('productos', ProductoController::class);
        Route::apiResource('ventas',    VentaController::class)->only(['index', 'store', 'show', 'destroy']);

        // Ingresos
        Route::get('/ingresos',               [IngresoController::class, 'index']);
        Route::post('/ingresos',              [IngresoController::class, 'store']);
        Route::delete('/ingresos/{ingreso}',  [IngresoController::class, 'destroy']);
    });
});
