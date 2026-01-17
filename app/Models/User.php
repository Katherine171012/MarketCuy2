<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // IMPORTANTE para la seguridad API

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; // Nombre de tu tabla
    protected $primaryKey = 'id_user'; // Tu PK personalizada

    // Los campos que se pueden llenar masivamente
    protected $fillable = [
        'id_cliente',
        'user_nombre',
        'user_email',
        'user_password',
        'user_rol',
        'estado_user'
    ];

    // Ocultar password y token en las respuestas JSON
    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    // Mapear el campo de password de Laravel al tuyo
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    // Relación: Un usuario pertenece a un cliente
    public function cliente()
    {
        // belongsTo(Modelo, 'tu_fk_local', 'pk_de_la_otra_tabla')
        return $this->belongsTo(Client::class, 'id_cliente', 'id_cliente');
    }
}
