<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            // Datos del Cliente
            'cli_nombre'    => 'required|string|max:255',
            'cli_ruc_ced'   => 'required|unique:clientes,cli_ruc_ced',
            'cli_direccion' => 'required|string',
            'cli_telefono'  => 'nullable|string',

            // Datos de Login (Email y Password)
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email', // Verifica que no exista en usuarios
                'regex:/^.+@.+\..+$/i' // <--- TU VALIDACIÓN ESTRICTA (arroba y punto)
            ],
            'password' => 'required|string|min:8|confirmed', // confirmed busca password_confirmation
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => 'El correo debe tener un formato válido con @ y extensión (ej. .com)',
            'cli_ruc_ced.unique' => 'Esa cédula/RUC ya está registrada.',
        ];
    }
}
