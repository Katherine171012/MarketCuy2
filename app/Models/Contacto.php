<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'contactos';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'tipo',
        'mensaje',
        'estado',
    ];
}
