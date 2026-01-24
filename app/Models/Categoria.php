<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Categoria extends Model
{
    protected $connection = 'pgsql';

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'cat_nombre',
        'estado_cat',
    ];

    /**
     * Invalidar cache automáticamente cuando se guarda una categoría
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('home_categorias_8');
        });
    }

    public static function listarActivas()
    {
        return self::where('estado_cat', 'ACT')
            ->orderBy('cat_nombre', 'ASC')
            ->get();
    }

    public static function obtenerParaHome(int $limite = 8)
    {
        return Cache::remember("home_categorias_{$limite}", 3600, function () use ($limite) {
            return self::where('estado_cat', 'ACT')
                ->orderBy('id_categoria', 'asc')
                ->limit($limite)
                ->get();
        });
    }
}

