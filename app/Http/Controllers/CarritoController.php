<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    protected $db;

    public function __construct(FirebaseService $firebase)
    {
        $this->db = $firebase->database();
    }

    // Agregar o actualizar producto en Firebase
    public function agregar(Request $request)
    {
        $user = Auth::user();
        $idProd = trim($request->id_producto);
        $path = "carts/{$user->id_cliente}/items/{$idProd}";

        $reference = $this->db->getReference($path);
        $snapshot = $reference->getSnapshot();

        if ($snapshot->exists()) {
            $current = $snapshot->getValue();
            $nuevaCant = $current['cantidad'] + 1;
            $reference->update([
                'cantidad' => $nuevaCant,
                'subtotal' => round($nuevaCant * $request->precio, 2)
            ]);
        } else {
            $reference->set([
                'id_producto' => $idProd,
                'nombre' => $request->nombre,
                'precio' => (float)$request->precio,
                'pro_imagen' => $request->pro_imagen,
                'cantidad' => 1,
                'subtotal' => (float)$request->precio
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // Obtener el carrito completo para la vista
    public function obtener()
    {
        $user = Auth::user();
        $items = $this->db->getReference("carts/{$user->id_cliente}/items")->getValue();
        return response()->json($items ?: []);
    }

    // Eliminar o modificar cantidad
    public function eliminar($id)
    {
        $user = Auth::user();
        $this->db->getReference("carts/{$user->id_cliente}/items/{$id}")->remove();
        return response()->json(['ok' => true]);
    }
}
