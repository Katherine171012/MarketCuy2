<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class HomeController extends Controller
{
    public function index()
    {
        // Traemos categorías (asegúrate que el método exista en el modelo, si no usa Categoria::take(8)->get())
        $categorias = Categoria::obtenerParaHome(8);

        // Traemos destacados
        $productosDestacados = Producto::obtenerDestacados(4);

        return view('home', compact('categorias', 'productosDestacados'));
    }

    // Lógica para el "Click Count"
    public function registrarClick($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            // 1. Sumamos el click
            $producto->increment('pro_clicks_count');

            // 2. CORRECCIÓN: Redirigimos al index con el parámetro view
            // porque NO tienes una ruta 'productos.show'
            return redirect()->route('productos.index', ['view' => $producto->id_producto]);
        }

        return redirect()->back();
    }


    public function nosotros()
    {
        return view('nosotros.index');
    }
}

