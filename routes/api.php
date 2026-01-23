<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController; // <-- ASEGÚRATE DE IMPORTAR ESTE

// --- RUTAS PÚBLICAS ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- NUEVAS RUTAS DEL CARRITO (POSTGRESQL) ---
    // Estas coinciden exactamente con los fetch() de tu Blade
    Route::get('/carrito', [CarritoController::class, 'obtener']);          // Leer
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar']); // Agregar/Incrementar
    Route::delete('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar']); // Borrar uno
    Route::delete('/carrito/vaciar', [CarritoController::class, 'vaciar']); // Borrar todo

    // --- RUTA DE CHECKOUT ---
    Route::post('/checkout-process', [\App\Http\Controllers\CheckoutController::class, 'process']);
});
