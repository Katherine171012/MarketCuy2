<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use Illuminate\Support\Facades\Log;

class FacturaController extends Controller
{
    /**
     * Procesa la compra desde el carrito (Firebase) y la guarda en SQL.
     */
    public function checkout(Request $request)
    {
        // Obtenemos el id_cliente desde el usuario que Sanctum ya identificó
        $user = $request->user();
        $idCliente = $user->id_cliente;

        try {
            // Usamos tu modelo tal como lo tienes
            $factura = Factura::crearFactura(
                $idCliente,
                "Compra E-commerce MarketCuy",
                $request->items
            );

            // Aprobamos la factura inmediatamente
            Factura::aprobarFactura($factura->id_factura);

            return response()->json([
                'ok' => true,
                'id_factura' => $factura->id_factura
            ]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }
    /**
     * Opcional: Para que el cliente vea sus pedidos en el prototipo 3
     */
    public function misPedidos(Request $request)
    {
        $idCliente = $request->user()->id_cliente; // O el que envíes por parámetro

        $pedidos = Factura::where('id_cliente', $idCliente)
            ->orderBy('fac_fecha_hora', 'desc')
            ->get();

        return response()->json($pedidos);
    }
}
