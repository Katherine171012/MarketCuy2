<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'proxfac'; // Tu tabla SQL
    public $timestamps = false;

    // Al ser tabla detalle sin ID autoincremental propio, desactivamos esto
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_factura',
        'id_producto',
        'pxf_cantidad',
        'pxf_precio',
        'pxf_subtotal',
        'estado_pxf' // 'ACT'
    ];
}
