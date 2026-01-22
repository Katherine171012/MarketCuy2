<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function obtener()
    {
        return response()->json(Carrito::obtenerResumen(Auth::id()));
    }

    public function agregar(Request $request)
    {
        try {
            Carrito::agregarOActualizar(Auth::id(), $request->id_producto, $request->cantidad ?? 1);
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function eliminar($id)
    {
        Carrito::where('id', $id)->where('id_user', Auth::id())->delete();
        return response()->json(['ok' => true]);
    }

    public function vaciar()
    {
        Carrito::where('id_user', Auth::id())->delete();
        return response()->json(['ok' => true]);
    }
}
