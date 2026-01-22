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
    public $timestamps = false; // Importante: Tu tabla clientes no tiene created_at/updated_at

    protected $fillable = [
        'id_cliente',
        'cli_nombre',
        'cli_ruc_ced',    // Esta es tu "identificación"
        'cli_telefono',
        'cli_mail',       // Este es el email del cliente
        'id_ciudad',
        'cli_celular',
        'cli_direccion',
        'estado_cli'
    ];




    // --- ESTE ES CLAVE PARA EL ECOMMERCE ---
    public static function generarSiguienteId() {
        // Tu lógica original para mantener el orden CLI0001, CLI0002...
        $ultimoId = self::where('id_cliente', 'LIKE', 'CLI%')
            ->selectRaw('MAX(CAST(SUBSTRING(id_cliente FROM 4) AS INTEGER)) as total')
            ->value('total');
        $siguiente = ($ultimoId ?? 0) + 1;
        return 'CLI' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }



    // --- RELACIONES ---

    public function ciudad() {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }

    // NUEVO: Relación inversa para saber si este cliente tiene usuario online
    public function usuario() {
        return $this->hasOne(User::class, 'id_cliente', 'id_cliente');
    }
}
