<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UnidadMedida;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
/* ====== Producto ====== */
class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'pro_nombre',
        'pro_descripcion',
        'pro_um_compra',
        'pro_um_venta',
        'pro_valor_compra',
        'pro_precio_venta',
        'pro_precio_antes',
        'pro_saldo_inicial',
        'pro_qty_ingresos',
        'pro_qty_egresos',
        'pro_qty_ajustes',
        'pro_saldo_final',
        'estado_prod',
        'id_categoria',
        'pro_etiqueta',
        'pro_es_destacado',
        'pro_clicks_count',
        'pro_imagen',
    ];

    public function enStock(): bool
    {
        return ((int)($this->pro_saldo_final ?? 0)) > 0;
    }

    public function tieneDescuento(): bool
    {
        return $this->pro_precio_antes !== null
            && $this->pro_precio_antes > $this->pro_precio_venta;
    }

    public function etiquetaPromo(): string
    {
        $t = trim((string) ($this->pro_etiqueta ?? ''));
        return $t !== '' ? $t : 'Oferta';
    }

    public static function obtenerOfertas(int $limite = 6)
    {
        return self::where('estado_prod', 'ACT')
            ->whereRaw('COALESCE(pro_saldo_final, 0) > 0')
            ->whereNotNull('pro_precio_antes')
            ->whereColumn('pro_precio_antes', '>', 'pro_precio_venta')
            ->with('categoria')
            ->orderBy('pro_nombre', 'asc')
            ->limit($limite)
            ->get();
    }

    public static function obtenerDestacados(int $limite = 4)
    {
        return self::where('estado_prod', 'ACT')
            ->where('pro_es_destacado', true)
            ->orderBy('pro_nombre', 'asc')
            ->limit($limite)
            ->get();
    }

    public function unidadCompra()
    {
        return $this->belongsTo(
            UnidadMedida::class,
            'pro_um_compra',
            'id_unidad_medida'
        );
    }

    public function unidadVenta()
    {
        return $this->belongsTo(
            UnidadMedida::class,
            'pro_um_venta',
            'id_unidad_medida'
        );
    }

    public function categoria()
    {
        return $this->belongsTo(
            Categoria::class,
            'id_categoria',
            'id_categoria'
        );
    }

    public static function obtenerActivos()
    {
        return self::where('estado_prod', 'ACT')
            ->with('categoria')
            ->orderByRaw("CAST(SUBSTRING(id_producto FROM 2) AS INTEGER) ASC")
            ->get();
    }

    public static function queryActivos()
    {
        return self::query()
            ->whereIn('estado_prod', ['ACT', 'INA'])
            ->with('categoria');
    }

    private static function aplicarOrdenMix($query, string $seed)
    {
        return $query->orderByRaw("md5(id_producto || ?)", [$seed]);
    }

    public static function obtenerParaLista(int $porPagina = 10, ?string $seed = null)
    {
        $q = self::query()
            ->with('categoria')
            ->orderByRaw("CASE
                WHEN estado_prod = 'ACT' THEN 1
                WHEN estado_prod = 'INA' THEN 2
                ELSE 3 END");

        if ($seed) {
            self::aplicarOrdenMix($q, $seed);
        } else {
            $q->orderByRaw("RANDOM()");
        }

        return $q->paginate($porPagina);
    }

    public static function paginarActivos(int $perPage = 10)
    {
        return self::queryActivos()
            ->orderByRaw("CAST(SUBSTRING(id_producto FROM 2) AS INTEGER) ASC")
            ->paginate($perPage);
    }

    public static function buscarPorId(?string $id): ?self
    {
        if (!$id) return null;

        return self::with(['categoria', 'unidadCompra', 'unidadVenta'])->find($id);
    }

    public static function paginarActivosConFiltros(
        ?string $orden,
        ?string $idCategoria,
        ?string $unidad,
        ?string $q,
        int $perPage = 10,
        ?string $seed = null
    ) {
        $query = self::queryActivos();

        if ($q !== null && trim($q) !== '') {
            $q = trim($q);
            $query->where('pro_nombre', 'ILIKE', '%' . $q . '%');
        }

        if ($idCategoria !== null && $idCategoria !== '') {
            $query->where('id_categoria', (int)$idCategoria);
        }

        if ($unidad !== null && $unidad !== '') {
            $query->where('pro_um_compra', $unidad);
        }

        $orden = ($orden !== null && $orden !== '') ? $orden : 'mix';

        switch ($orden) {
            case 'mix':
                if ($seed) {
                    self::aplicarOrdenMix($query, $seed);
                } else {
                    $query->orderByRaw("RANDOM()");
                }
                break;

            case 'id_asc':
                $query->orderByRaw("CAST(SUBSTRING(id_producto FROM 2) AS INTEGER) ASC");
                break;

            case 'id_desc':
                $query->orderByRaw("CAST(SUBSTRING(id_producto FROM 2) AS INTEGER) DESC");
                break;

            case 'nombre_az':
                $query->orderBy('pro_nombre', 'ASC');
                break;

            case 'nombre_za':
                $query->orderBy('pro_nombre', 'DESC');
                break;

            default:
                return null;
        }

        return $query->paginate($perPage);
    }

    public static function existeNombre(string $nombre): bool
    {
        return self::where('pro_nombre', $nombre)->exists();
    }

    public static function existeDescripcion(string $desc): bool
    {
        return self::where('pro_descripcion', $desc)->exists();
    }

    public static function existeId(string $id): bool
    {
        return self::where('id_producto', $id)->exists();
    }

    public static function generarSiguienteId(): string
    {
        $base = 1000;

        $max = (int) self::query()
            ->where('id_producto', 'like', 'P%')
            ->selectRaw("COALESCE(MAX(CAST(SUBSTRING(id_producto FROM 2) AS INTEGER)), 0) AS max_id")
            ->value('max_id');

        if ($max < ($base - 1)) {
            return 'P' . $base;
        }

        return 'P' . ($max + 1);
    }

    public static function crearProducto(array $data)
    {
        $idProducto = $data['id_producto'] ?? self::generarSiguienteId();

        return self::create([
            'id_producto'       => $idProducto,
            'pro_nombre'        => $data['pro_nombre'],
            'pro_descripcion'   => $data['pro_descripcion'] ?? null,

            'pro_um_compra'     => $data['unidad_medida'],
            'pro_um_venta'      => $data['unidad_medida'],

            'pro_valor_compra'  => $data['pro_valor_compra'] ?? 0,
            'pro_precio_venta'  => $data['pro_precio_venta'],
            'pro_precio_antes'  => $data['pro_precio_antes'] ?? null,

            'pro_saldo_inicial' => $data['pro_saldo_inicial'],

            'pro_qty_ingresos'  => 0,
            'pro_qty_egresos'   => 0,
            'pro_qty_ajustes'   => 0,

            'pro_saldo_final'   => $data['pro_saldo_inicial'],
            'estado_prod'       => 'ACT',

            'id_categoria'      => (int) $data['id_categoria'],

            'pro_etiqueta'      => $data['pro_etiqueta'] ?? null,
            'pro_es_destacado'  => $data['pro_es_destacado'] ?? false,
            'pro_clicks_count'  => $data['pro_clicks_count'] ?? 0,

            'pro_imagen'        => $data['pro_imagen'] ?? null,
        ]);
    }

    public function actualizarProducto(array $data)
    {
        return $this->update([
            'pro_valor_compra'  => $data['pro_valor_compra'] ?? $this->pro_valor_compra,
            'pro_precio_venta'  => $data['pro_precio_venta'],
            'pro_precio_antes'  => $data['pro_precio_antes'] ?? $this->pro_precio_antes,

            'pro_saldo_inicial' => (int) $data['pro_saldo_inicial'],
            'pro_qty_ingresos'  => (int) $data['pro_qty_ingresos'],
            'pro_qty_egresos'   => (int) $data['pro_qty_egresos'],
            'pro_qty_ajustes'   => (int) $data['pro_qty_ajustes'],
            'pro_saldo_final'   => (int) $data['pro_saldo_final'],

            'id_categoria'      => isset($data['id_categoria']) ? (int)$data['id_categoria'] : $this->id_categoria,
        ]);
    }

    public function inactivarProducto()
    {
        return $this->update(['estado_prod' => 'INA']);
    }

    public static function crearProductoTx(array $data)
    {
        try {
            DB::beginTransaction();
            self::crearProducto($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function actualizarProductoTx(array $data)
    {
        try {
            DB::beginTransaction();
            $this->actualizarProducto($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function inactivarProductoTx()
    {
        try {
            DB::beginTransaction();
            $this->inactivarProducto();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
