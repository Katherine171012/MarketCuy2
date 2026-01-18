<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;     // Usaremos este
use App\Http\Controllers\CheckoutController; // Para el futuro pago
Route::get('/ciudades', [AuthController::class, 'getCiudades']);
// --- RUTAS PÚBLICAS ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS (Requieren Token Bearer) ---
Route::middleware('auth:sanctum')->group(function () {

    // 1. Usuario
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 2. Carrito (Firebase)
    // Estas URLs deben ser idénticas a las del fetch en tu JS
    Route::get('/cart/data', [CartController::class, 'getCart']);          // Leer carrito
    Route::post('/cart-add', [CartController::class, 'addToCart']);        // Agregar item
    Route::post('/cart/update', [CartController::class, 'updateQuantity']);// Subir/Bajar cantidad
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeItem']); // Borrar item

    // 3. Checkout (Futuro)
    Route::post('/checkout-process', [CheckoutController::class, 'process']);
});
