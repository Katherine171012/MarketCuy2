<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Validar usuario autenticado
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Leer Carrito de PostgreSQL
        $carritoData = Carrito::obtenerResumen($user->id_user);

        if (!$carritoData['items'] || $carritoData['items']->isEmpty()) {
            return response()->json(['error' => 'Carrito vacío'], 400);
        }

        try {
            // Preparar items para la función SQL
            $itemsParaSql = [];
            foreach ($carritoData['items'] as $item) {
                $itemsParaSql[] = [
                    'id_producto' => $item->id_producto,
                    'cantidad' => $item->cantidad
                ];
            }

            // GUARDADO EN SQL (Facturas + ProxFac + Actualización de Stock)
            $factura = Factura::crearVentaWeb($user->id_cliente, $itemsParaSql);

            // Limpiar Carrito de PostgreSQL
            Carrito::where('id_user', $user->id_user)->delete();

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
