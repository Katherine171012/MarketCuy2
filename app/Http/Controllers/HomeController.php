<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Categorías (Usamos tu método del modelo)
        $categorias = Categoria::obtenerParaHome(8); // Trae 8 categorías

        // 2. Productos Destacados (Usamos tu método del modelo)
        // Esto filtra automáticamente por 'ACT' y 'pro_es_destacado' = true
        $productosDestacados = Producto::obtenerDestacados(4);

        return view('home', compact('categorias', 'productosDestacados'));
    }

    // Lógica para el "Click Count"
    public function registrarClick($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            // Incrementamos el contador
            $producto->increment('pro_clicks_count');

            // Redirigimos a la vista de detalle real (show)
            // Asumo que tu ruta de detalle es 'productos.show'
            return redirect()->route('productos.show', $producto->id_producto);
        }

        return redirect()->route('home')->with('error', 'Producto no encontrado');
    }
}
