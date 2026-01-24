<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Carrito extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'carrito';
    protected $fillable = ['id_user', 'id_producto', 'cantidad'];

    const IVA_RATE = 0.15; // 15% IVA
    const CACHE_TTL = 30; // 30 segundos de cache

    // ==================== RELACIONES ====================

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    // ==================== MÉTODOS DE NEGOCIO ====================

    /**
     * Agregar o incrementar cantidad de un producto en el carrito
     * Valida stock disponible antes de agregar
     */
    public static function agregarOActualizar($userId, $idProd, $cantidad)
    {
        $producto = Producto::findOrFail($idProd);

        $item = self::where('id_user', $userId)
            ->where('id_producto', $idProd)
            ->first();

        $nuevaCantidad = $item ? ($item->cantidad + $cantidad) : $cantidad;

        if ($nuevaCantidad > $producto->pro_saldo_final) {
            throw new \Exception("Stock insuficiente. Solo quedan {$producto->pro_saldo_final} unidades.");
        }

        if ($nuevaCantidad <= 0) {
            if ($item)
                $item->delete();
            self::invalidarCache($userId);
            return null;
        }

        if ($item) {
            $item->update(['cantidad' => $nuevaCantidad]);
        } else {
            $item = self::create([
                'id_user' => $userId,
                'id_producto' => $idProd,
                'cantidad' => $cantidad
            ]);
        }

        self::invalidarCache($userId);
        return $item;
    }

    /**
     * Eliminar un item específico del carrito por su ID
     */
    public static function eliminarItem($userId, $idCarrito)
    {
        $deleted = self::where('id', $idCarrito)
            ->where('id_user', $userId)
            ->delete();

        self::invalidarCache($userId);
        return $deleted > 0;
    }

    /**
     * Vaciar todo el carrito del usuario
     */
    public static function vaciarCarrito($userId)
    {
        self::where('id_user', $userId)->delete();
        self::invalidarCache($userId);
    }

    /**
     * Obtener resumen completo del carrito con totales calculados
     * Incluye: items, subtotal, iva, total
     * OPTIMIZADO: Usa eager loading
     */
    public static function obtenerResumen($userId)
    {
        // NOTA: Cache deshabilitado temporalmente - Laravel no serializa bien modelos Eloquent
        // TODO: Implementar cache con array serializable en lugar de modelos

        // Eager loading para evitar N+1 queries
        $items = self::where('id_user', $userId)
            ->with(['producto.categoria'])
            ->get();

        $subtotal = $items->sum(function ($item) {
            return $item->producto ? ($item->cantidad * $item->producto->pro_precio_venta) : 0;
        });

        $iva = round($subtotal * self::IVA_RATE, 2);
        $total = round($subtotal + $iva, 2);

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'iva' => $iva,
            'total' => $total,
            'conteo' => $items->sum('cantidad')
        ];
    }

    /**
     * Obtener solo el conteo total de items (para el badge del nav)
     */
    public static function obtenerConteo($userId)
    {
        return self::where('id_user', $userId)->sum('cantidad');
    }

    // ==================== CACHE MANAGEMENT ====================
    // DESHABILITADO: Laravel no serializa bien modelos Eloquent en cache

    /**
     * Invalidar cache del carrito cuando hay cambios
     */
    protected static function invalidarCache($userId)
    {
        // Cache deshabilitado temporalmente
        // Cache::forget("cart_summary_{$userId}");
        // Cache::forget("cart_count_{$userId}");
    }
}
