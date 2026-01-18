<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $primaryKey = 'id_factura';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'id_cliente',
        'fac_descripcion',
        'fac_fecha_hora',
        'fac_subtotal',
        'fac_iva',
        'estado_fac',
        'canal_venta' // 'EC' para Ecommerce
    ];

    // Generar ID FCT0001, FCT0002...
    public static function generarId()
    {
        $ultimo = DB::table('facturas')
            ->where('id_factura', 'like', 'FCT%')
            ->orderBy('id_factura', 'desc')
            ->first();

        if (!$ultimo) return 'FCT0001';

        $num = intval(substr($ultimo->id_factura, 3));
        return 'FCT' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
    }

    // LÓGICA TRANSACCIONAL SQL
    public static function crearVentaWeb($idCliente, $itemsCarrito)
    {
        // Iniciamos transacción SQL (Todo o Nada)
        DB::beginTransaction();
        try {
            $nuevoId = self::generarId();
            $subtotalAcumulado = 0;

            // 1. Crear Cabecera (Inicial)
            $factura = self::create([
                'id_factura'      => $nuevoId,
                'id_cliente'      => $idCliente,
                'fac_descripcion' => 'VENTA WEB',
                'fac_fecha_hora'  => now(),
                'fac_subtotal'    => 0,
                'fac_iva'         => 0,
                'estado_fac'      => 'APR', // APROBADA DE UNA VEZ
                'canal_venta'     => 'EC'
            ]);

            // 2. Procesar Detalles y Stock
            foreach ($itemsCarrito as $item) {
                // Bloqueamos el producto para evitar errores de stock simultáneos
                $producto = Producto::where('id_producto', $item['id_producto'])
                    ->lockForUpdate()
                    ->first();

                if ($item['cantidad'] > $producto->pro_saldo_final) {
                    throw new \Exception("Stock insuficiente para: " . $producto->pro_nombre);
                }

                $precio = $producto->pro_precio_venta;
                $lineaTotal = $precio * $item['cantidad'];
                $subtotalAcumulado += $lineaTotal;

                // Insertar en proxfac
                DetalleFactura::create([
                    'id_factura'   => $nuevoId,
                    'id_producto'  => $producto->id_producto,
                    'pxf_cantidad' => $item['cantidad'],
                    'pxf_precio'   => $precio,
                    'pxf_subtotal' => $lineaTotal,
                    'estado_pxf'   => 'ACT'
                ]);

                // Actualizar Stock en SQL
                $producto->pro_saldo_final -= $item['cantidad'];
                $producto->pro_qty_egresos += $item['cantidad'];
                $producto->save();
            }

            // 3. Actualizar Totales con IVA 15%
            $iva = $subtotalAcumulado * 0.15;

            $factura->update([
                'fac_subtotal' => $subtotalAcumulado,
                // Guardamos el IVA redondeado o entero según tu BD
                'fac_iva'      => (int)round($iva)
            ]);

            DB::commit(); // Confirmar cambios en SQL
            return $factura;

        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, cancelar todo en SQL
            throw $e;
        }
    }
}
