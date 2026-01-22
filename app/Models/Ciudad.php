<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'ciudades';
    protected $primaryKey = 'id_ciudad';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Excelente, las tablas legacy suelen no tener esto

    protected $fillable = ['id_ciudad', 'ciu_descripcion'];
}
