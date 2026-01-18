<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura; // Modelo SQL
use App\Services\FirebaseService; // Servicio NoSQL
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $db;

    public function __construct(FirebaseService $firebase)
    {
        $this->db = $firebase->database();
    }

    public function process(Request $request)
    {
        // A. Validar usuario
        $user = auth()->user();
        $uid = "client_" . trim($user->id_cliente);

        // B. Leer Carrito de Firebase
        $cartRef = $this->db->getReference("carts/$uid");
        $cartData = $cartRef->getValue();

        if (!$cartData || empty($cartData['items'])) {
            return response()->json(['error' => 'Carrito vacío'], 400);
        }

        try {
            // C. GUARDADO EN SQL (Aquí ocurre la magia relacional)
            // Preparamos los items para la función SQL
            $itemsParaSql = [];
            foreach ($cartData['items'] as $item) {
                $itemsParaSql[] = [
                    'id_producto' => $item['id_producto'],
                    'cantidad'    => $item['cantidad']
                ];
            }

            // ¡¡¡ AQUÍ SE INSERTA EN FACTURAS Y PROXFAC !!!
            $factura = Factura::crearVentaWeb($user->id_cliente, $itemsParaSql);


            // D. GUARDADO EN NOSQL (Solo datos de envío/logística)
            // Usamos el ID de la factura SQL (ej: FCT0050) como clave
            $this->db->getReference('orders/' . $factura->id_factura)->set([
                'id_factura'     => $factura->id_factura,
                'cliente_nombre' => $request->nombre_completo,
                'direccion'      => $request->direccion,
                'ciudad'         => $request->ciudad,
                'telefono'       => $request->telefono,
                'items'          => $cartData['items'],
                'metodo_pago'    => $request->pago,
                'notas'          => $request->notas,
                'total_pagado'   => $factura->fac_subtotal + $factura->fac_iva,
                'estado_envio'   => 'PREPARANDO',
                'fecha'          => now()->toDateTimeString()
            ]);

            // E. Limpiar Carrito
            $cartRef->remove();

            return response()->json([
                'ok' => true,
                'order_id' => $factura->id_factura
            ]);

        } catch (\Exception $e) {
            Log::error("Error Checkout: " . $e->getMessage());
            return response()->json(['error' => 'Error al procesar: ' . $e->getMessage()], 500);
        }
    }
}
