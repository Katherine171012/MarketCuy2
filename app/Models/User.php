<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'pgsql';
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'id_cliente',
        'user_nombre',
        'user_email',
        'user_password',
        'user_rol',
        'estado_user'
    ];

    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->user_password;
    }
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'id_cliente', 'id_cliente');
    }
}
