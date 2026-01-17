<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
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

    // --- MÉTODOS EXISTENTES (NO LOS BORRES) ---
    public static function obtenerActivos() {
        return self::where('estado_cli', 'ACT')->orderBy('cli_nombre', 'ASC')->get();
    }

    public static function consultarPorParametro($campo, $valor, $porPagina = 10) {
        $query = self::with('ciudad');
        if ($campo === 'id_ciudad') {
            $query->whereRaw("trim(id_ciudad) = ?", [trim($valor)]);
        } else {
            $query->where($campo, 'ILIKE', "%{$valor}%");
        }
        return $query->orderByRaw("CASE WHEN estado_cli = 'ACT' THEN 1 WHEN estado_cli = 'SUS' THEN 2 ELSE 3 END")
            ->paginate($porPagina);
    }

    public static function crearCliente(array $datos) {
        return self::create($datos);
    }

    // --- ESTE ES CLAVE PARA EL ECOMMERCE ---
    public static function generarSiguienteId() {
        // Tu lógica original para mantener el orden CLI0001, CLI0002...
        $ultimoId = self::where('id_cliente', 'LIKE', 'CLI%')
            ->selectRaw('MAX(CAST(SUBSTRING(id_cliente FROM 4) AS INTEGER)) as total')
            ->value('total');
        $siguiente = ($ultimoId ?? 0) + 1;
        return 'CLI' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function actualizarCliente(array $datos) {
        return $this->update($datos);
    }

    public function eliminarCliente() {
        return $this->update(['estado_cli' => 'INA']);
    }

    public static function obtenerParaLista($porPagina = 10) {
        return self::with('ciudad')
            ->orderByRaw("CASE WHEN estado_cli = 'ACT' THEN 1 WHEN estado_cli = 'SUS' THEN 2 ELSE 3 END")
            ->orderBy('id_cliente', 'asc')
            ->paginate($porPagina);
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
