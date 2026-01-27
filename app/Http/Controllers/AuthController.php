<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function getCiudades()
    {
        $ciudades = Ciudad::orderBy('id_ciudad', 'asc')->get();
        return response()->json($ciudades);
    }

    public function register(Request $request)
    {
        $request->validate([
            'identificacion' => 'required|string|max:13',
            'nombre'         => 'required|string|max:255',
            'id_ciudad'      => 'required|exists:ciudades,id_ciudad',
            'direccion'      => 'nullable|string',
            'telefono'       => 'nullable|string',
            'celular'        => 'nullable|string',

            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                ->letters()
                ->numbers()
                ->mixedCase()
                ->symbols()
            ],

            'email'          => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,user_email',
                'regex:/^.+@.+\..+$/i'
            ],
        ], [
            'email.regex'    => 'El correo debe tener un formato válido (ejemplo: nombre@dominio.com)',
            'email.unique'   => 'Este correo ya está registrado.',
            'password.min'   => 'La contraseña debe tener al menos 8 caracteres.'
        ]);


        return DB::transaction(function () use ($request) {

            $cliente = Cliente::where('cli_ruc_ced', $request->identificacion)->first();

            if (!$cliente) {
                $cliente = Cliente::create([
                    'id_cliente'    => Cliente::generarSiguienteId(),
                    'cli_nombre'    => $request->nombre,
                    'cli_ruc_ced'   => $request->identificacion,
                    'cli_mail'      => $request->email,
                    'cli_direccion' => $request->direccion,
                    'cli_telefono'  => $request->telefono,
                    'cli_celular'   => $request->celular,
                    'id_ciudad'     => $request->id_ciudad,
                    'estado_cli'    => 'ACT'
                ]);
            }

            $user = User::create([
                'id_cliente'    => $cliente->id_cliente,
                'user_nombre'   => $request->nombre,
                'user_email'    => $request->email,
                'user_password' => Hash::make($request->password),
                'user_rol'      => 'customer',
                'estado_user'   => 'ACT'
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registro exitoso',
                'token'   => $token,
                'user'    => [
                    'id'          => $user->id_user,
                    'user_nombre' => $user->user_nombre,
                    'user_email'  => $user->user_email,
                    'rol'         => $user->user_rol
                ]
            ], 201);
        });
    }

    public function login(Request $request)
    {
        // Validar inputs
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario por su email personalizado
        $user = User::where('user_email', $request->email)->first();

        // Verificar contraseña encriptada
        if (!$user || !Hash::check($request->password, $user->user_password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        //  Verificar estado
        if ($user->estado_user !== 'ACT') {
            return response()->json([
                'message' => 'Su cuenta se encuentra inactiva. Contacte al administrador.'
            ], 403);
        }

        // Generar Token
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Bienvenido',
            'token'   => $token,
            'user'    => [
                'id'          => $user->id_user,
                'id_cliente'  => $user->id_cliente,
                'user_nombre' => $user->user_nombre,
                'user_email'  => $user->user_email,
                'rol'         => $user->user_rol
            ]
        ], 200);
    }


    public function logout(Request $request)
    {
        // Revoca el token actual que se usó para la petición
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
