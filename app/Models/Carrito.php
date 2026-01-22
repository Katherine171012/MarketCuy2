<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $connection = 'pgsql_cloud';
    protected $table = 'carrito';
    protected $fillable = ['id_user', 'id_producto', 'cantidad'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public static function agregarOActualizar($userId, $idProd, $cantidad)
    {
        $producto = Producto::on('pgsql_cloud')->findOrFail($idProd);

        $item = self::where('id_user', $userId)
            ->where('id_producto', $idProd)
            ->first();

        $nuevaCantidad = $item ? ($item->cantidad + $cantidad) : $cantidad;

        if ($nuevaCantidad > $producto->pro_saldo_final) {
            throw new \Exception("Stock insuficiente. Solo quedan {$producto->pro_saldo_final} unidades.");
        }

        if ($item) {
            $item->update(['cantidad' => $nuevaCantidad]);
            return $item;
        }

        return self::create([
            'id_user' => $userId,
            'id_producto' => $idProd,
            'cantidad' => $cantidad
        ]);
    }

    public static function obtenerResumen($userId)
    {
        $items = self::where('id_user', $userId)
            ->with(['producto.categoria']) // Cargamos relación anidada si la necesitas
            ->get();

        $subtotal = $items->sum(function($item) {
            return $item->producto ? ($item->cantidad * $item->producto->pro_precio_venta) : 0;
        });

        // Retornamos 'subtotal' para que el JS del Blade lo reconozca
        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'conteo' => $items->sum('cantidad')
        ];
    }
}
