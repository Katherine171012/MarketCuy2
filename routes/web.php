<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactoController;



/*
|--------------------------------------------------------------------------
| 1. Portada Principal
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', function () { return redirect()->route('home'); });
Route::get('/nosotros', [HomeController::class, 'nosotros'])->name('nosotros.index');

/*
|--------------------------------------------------------------------------
| 3. Dashboard (Zona Privada)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () { return view('login'); })->name('login');
Route::get('/registro', function () { return view('register'); })->name('register');

/*
|--------------------------------------------------------------------------
| 4. Módulo de Productos
|--------------------------------------------------------------------------
*/
Route::get('/producto/ver/{id}', [HomeController::class, 'registrarClick'])->name('producto.click');

Route::prefix('productos')->group(function () {

    // Rutas de información / redirección
    Route::get('/menu', function () {
        return redirect()->route('home');
    })->name('productos.menu');

    // Apunta a tu ProductoController (que carga views/productos/index.blade.php)
    Route::get('/', [ProductoController::class, 'index'])->name('productos.index');

    Route::post('/guardar', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::post('/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar.post');
    Route::put('/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});

Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

/*
|--------------------------------------------------------------------------
| 4. Rutas del Carrito (CORREGIDO SEGÚN TU FOTO)
|--------------------------------------------------------------------------
*/

// Vista del Carrito -> Apunta a views/shop/carrito.blade.php
Route::get('/cart/view', function () {
    return view('shop.carrito');
})->name('cart.view');

// Redirección amigable
Route::get('/carrito', function () {
    return redirect()->route('cart.view');
})->name('cart.index');

// Vista Checkout -> Apunta a views/shop/checkout.blade.php
Route::get('/finalizar-compra', function () {
    return view('shop.checkout');
})->name('checkout.index');

// Redirección de seguridad para evitar errores 404
Route::get('/shop', function() {
    return redirect()->route('productos.index');
});
// Vista de Confirmación de Pedido
Route::get('/confirmacion/{id}', function ($id) {
    return view('shop.confirmation', ['orderId' => $id]);
})->name('order.confirmation');
