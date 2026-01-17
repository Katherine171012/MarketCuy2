<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class HomeController extends Controller
{
    public function index()
    {
        $categorias = Categoria::obtenerParaHome(8); // Trae 8 categorías

        $productosDestacados = Producto::obtenerDestacados(4);

        return view('home', compact('categorias', 'productosDestacados'));
    }

    // Lógica para el "Click Count"
    public function registrarClick($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $producto->increment('pro_clicks_count');

            return redirect()->route('productos.show', $producto->id_producto);
        }

        return redirect()->back();
    }
}
