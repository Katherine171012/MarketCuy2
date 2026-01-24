<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    /**
     * Obtener resumen completo del carrito
     */
    public function obtener()
    {
        try {
            return response()->json(Carrito::obtenerResumen(Auth::id()));
        } catch (\Exception $e) {
            \Log::error('Error 500 en CarritoController@obtener: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Agregar o actualizar cantidad de producto
     */
    public function agregar(Request $request)
    {
        try {
            // Validar datos básicos
            $request->validate([
                'id_producto' => 'required|string',
                'cantidad' => 'required|integer'
            ]);

            $producto = Producto::find($request->id_producto);

            // Validar que el producto existe
            if (!$producto) {
                return response()->json([
                    'error' => config('mensajes.carrito.producto.no.encontrado')
                ], 404);
            }

            // Validar que el producto está activo
            if ($producto->estado_prod !== 'ACT') {
                return response()->json([
                    'error' => config('mensajes.carrito.producto.inactivo')
                ], 400);
            }

            // Validar cantidad
            $cantidad = (int) $request->cantidad;
            if ($cantidad === 0) {
                return response()->json([
                    'error' => config('mensajes.carrito.cantidad.invalida')
                ], 400);
            }

            // Obtener item actual del carrito si existe
            $itemActual = Carrito::where('id_user', Auth::id())
                ->where('id_producto', $request->id_producto)
                ->first();

            $cantidadActual = $itemActual ? $itemActual->cantidad : 0;
            $nuevaCantidad = $cantidadActual + $cantidad;

            // Validar stock disponible
            $stockDisponible = (int) $producto->pro_saldo_final;

            if ($stockDisponible <= 0) {
                return response()->json([
                    'error' => config('mensajes.carrito.sin.stock')
                ], 400);
            }

            if ($nuevaCantidad > $stockDisponible) {
                $mensaje = $stockDisponible === 1
                    ? 'Solo tenemos 1 unidad disponible'
                    : str_replace(':cantidad', $stockDisponible, config('mensajes.carrito.stock.insuficiente'));

                return response()->json([
                    'error' => $mensaje,
                    'stock_disponible' => $stockDisponible
                ], 400);
            }

            // Agregar/actualizar en el carrito
            Carrito::agregarOActualizar(
                Auth::id(),
                $request->id_producto,
                $cantidad
            );

            return response()->json([
                'ok' => true,
                'message' => $cantidad > 0
                    ? config('mensajes.carrito.actualizar.ok')
                    : config('mensajes.carrito.agregar.ok')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => config('mensajes.carrito.cantidad.invalida')
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en CarritoController@agregar: ' . $e->getMessage());
            return response()->json([
                'error' => config('mensajes.carrito.error.agregar')
            ], 500);
        }
    }

    /**
     * Eliminar un item específico
     */
    public function eliminar($id)
    {
        try {
            $success = Carrito::eliminarItem(Auth::id(), $id);

            if ($success) {
                return response()->json([
                    'ok' => true,
                    'message' => config('mensajes.carrito.eliminar.ok')
                ]);
            }

            return response()->json([
                'error' => config('mensajes.carrito.error.eliminar')
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error en CarritoController@eliminar: ' . $e->getMessage());
            return response()->json([
                'error' => config('mensajes.carrito.error.eliminar')
            ], 500);
        }
    }

    /**
     * Vaciar todo el carrito
     */
    public function vaciar()
    {
        try {
            Carrito::vaciarCarrito(Auth::id());
            return response()->json([
                'ok' => true,
                'message' => config('mensajes.carrito.vaciar.ok')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en CarritoController@vaciar: ' . $e->getMessage());
            return response()->json([
                'error' => 'No pudimos vaciar el carrito. Intenta nuevamente'
            ], 500);
        }
    }
}
