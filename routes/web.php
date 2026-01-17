<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| 1. Portada Principal (Pública)
|--------------------------------------------------------------------------
*/

// Esta es tu página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Redirección por si alguien escribe /home manualmente
Route::get('/home', function () {
    return redirect()->route('home');
});

/*
|--------------------------------------------------------------------------
| 2. Autenticación (Vistas)
|--------------------------------------------------------------------------
| Estas se abrirán cuando el usuario intente añadir al carrito
*/

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/registro', function () {
    return view('register');
})->name('register');

/*
|--------------------------------------------------------------------------
| 3. Dashboard (Zona Privada)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| 4. Módulo de Productos
|--------------------------------------------------------------------------
*/

// Registro de clicks individual
Route::get('/producto/ver/{id}', [HomeController::class, 'registrarClick'])->name('producto.click');

Route::prefix('productos')->group(function () {

    // Rutas de información / redirección
    Route::get('/menu', function () {
        return redirect()->route('home');
    })->name('productos.menu');

    Route::get('/consultar', function () {
        return redirect()->route('home');
    })->name('productos.consultar');

    // CRUD y Listados
    Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/guardar', [ProductoController::class, 'store'])->name('productos.store');

    // Búsqueda
    Route::get('/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::post('/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar.post');

    // Acciones por ID
    Route::put('/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});

Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');


// RUTA HOME - AGREGAR ESTA LÍNEA
Route::get('/', function () {
    return view('welcome'); // o 'home' si tienes esa vista
})->name('home');

// Resto de tus rutas...
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('productos.show');

Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');
