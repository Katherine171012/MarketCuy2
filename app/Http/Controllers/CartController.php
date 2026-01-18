<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log; // Importante para ver errores

class CartController extends Controller
{
    protected $db;

    public function __construct(FirebaseService $firebase)
    {
        $this->db = $firebase->database();
    }

    // Función auxiliar para obtener el ID del usuario limpio
    private function getUserId() {
        $user = auth()->user();
        // IMPORTANTE: 'trim' quita los espacios en blanco del CHAR(7) de Postgres
        return "client_" . trim($user->id_cliente);
    }

    // 1. AGREGAR AL CARRITO
    public function addToCart(Request $request)
    {
        // Esto escribirá en storage/logs/laravel.log
        Log::info('Intento de agregar al carrito', ['user' => auth()->id(), 'data' => $request->all()]);

        try {
            $uid = $this->getUserId();
            $pid = trim($request->id_producto); // ID del producto (ej: PROD01)

            // Referencia a Firebase: carts/client_CLI001/items/PROD01
            $ref = $this->db->getReference("carts/$uid/items/$pid");
            $snapshot = $ref->getSnapshot();

            if ($snapshot->exists()) {
                // Si ya existe, sumamos la cantidad
                $item = $snapshot->getValue();
                $nuevaCant = $item['cantidad'] + $request->cantidad;

                $ref->update([
                    'cantidad' => $nuevaCant,
                    'subtotal' => round($item['precio'] * $nuevaCant, 2)
                ]);
            } else {
                // Si no existe, creamos el nodo
                $ref->set([
                    'id_producto' => $pid,
                    'nombre'      => $request->nombre,
                    'precio'      => (float)$request->precio,
                    'cantidad'    => (int)$request->cantidad,
                    'imagen'      => $request->imagen,
                    'subtotal'    => round((float)$request->precio * (int)$request->cantidad, 2)
                ]);
            }

            return response()->json(['ok' => true, 'message' => 'Producto guardado en Firebase']);

        } catch (\Exception $e) {
            Log::error('Error en Firebase Add: ' . $e->getMessage());
            return response()->json(['error' => 'Error conectando con Firebase'], 500);
        }
    }

    // 2. OBTENER CARRITO
    public function getCart()
    {
        try {
            $uid = $this->getUserId();
            $cart = $this->db->getReference('carts/' . $uid)->getValue();
            return response()->json($cart ?? ['items' => []]);
        } catch (\Exception $e) {
            return response()->json(['items' => []]);
        }
    }

    // 3. ACTUALIZAR CANTIDAD (+ / -)
    public function updateQuantity(Request $request)
    {
        $uid = $this->getUserId();
        $pid = trim($request->id_producto);
        $qty = $request->cantidad;

        $ref = $this->db->getReference("carts/$uid/items/$pid");

        if ($qty <= 0) {
            $ref->remove();
        } else {
            $item = $ref->getValue();
            if($item) {
                $ref->update([
                    'cantidad' => $qty,
                    'subtotal' => $item['precio'] * $qty
                ]);
            }
        }
        return response()->json(['ok' => true]);
    }

    // 4. ELIMINAR ITEM
    public function removeItem($id)
    {
        $uid = $this->getUserId();
        $this->db->getReference("carts/$uid/items/$id")->remove();
        return response()->json(['ok' => true]);
    }

}
