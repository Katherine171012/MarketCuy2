<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    /**
     * Obtener resumen completo del carrito
     */
    public function obtener()
    {
        return response()->json(Carrito::obtenerResumen(Auth::id()));
    }

    /**
     * Agregar o actualizar cantidad de producto
     */
    public function agregar(Request $request)
    {
        try {
            Carrito::agregarOActualizar(
                Auth::id(),
                $request->id_producto,
                $request->cantidad ?? 1
            );
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Eliminar un item específico
     */
    public function eliminar($id)
    {
        $success = Carrito::eliminarItem(Auth::id(), $id);
        return response()->json(['ok' => $success]);
    }

    /**
     * Vaciar todo el carrito
     */
    public function vaciar()
    {
        Carrito::vaciarCarrito(Auth::id());
        return response()->json(['ok' => true]);
    }
}
