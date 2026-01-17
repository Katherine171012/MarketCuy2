<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\HomeController;


// Portada
Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/home', function () {
    return redirect()->route('home');
});

Route::get('/producto/ver/{id}', [HomeController::class, 'registrarClick'])->name('producto.click');
//Productos
Route::prefix('productos')->group(function () {

    Route::get('/menu', function () {
        return redirect()->route('home');
    })->name('productos.menu');

    Route::get('/consultar', function () {
        return redirect()->route('home');
    })->name('productos.consultar');

    Route::get('/', [ProductoController::class, 'index'])
        ->name('productos.index');

    Route::post('/guardar', [ProductoController::class, 'store'])
        ->name('productos.store');

    Route::get('/buscar', [ProductoController::class, 'buscar'])
        ->name('productos.buscar');

    Route::post('/buscar', [ProductoController::class, 'buscar'])
        ->name('productos.buscar.post');

    Route::put('/{id}', [ProductoController::class, 'update'])
        ->name('productos.update');

    Route::delete('/{id}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy');
});
