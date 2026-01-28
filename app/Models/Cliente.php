<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id_cliente',
        'cli_nombre',
        'cli_ruc_ced',
        'cli_telefono',
        'cli_mail',
        'id_ciudad',
        'cli_celular',
        'cli_direccion',
        'estado_cli'
    ];


    public static function generarSiguienteId() {
         $ultimoId = self::where('id_cliente', 'LIKE', 'CLI%')
            ->selectRaw('MAX(CAST(SUBSTRING(id_cliente FROM 4) AS INTEGER)) as total')
            ->value('total');
        $siguiente = ($ultimoId ?? 0) + 1;
        return 'CLI' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

 public function ciudad() {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }


}
