<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// Ojo: Usamos Cliente, no Client

class AuthController extends Controller
{
    public function getCiudades()
    {
        $ciudades = Ciudad::orderBy('ciu_descripcion', 'asc')->get();
        return response()->json($ciudades);
    }

    public function register(Request $request)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'identificacion' => 'required|string|max:13',
            'nombre'         => 'required|string',
            'email'          => 'required|email|unique:users,user_email',
            'password'       => 'required|min:8',
            'id_ciudad'      => 'required|exists:ciudades,id_ciudad',
            'direccion'      => 'nullable|string',
            'telefono'       => 'nullable|string',
            'celular'   => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {

            // 2. BUSCAR O CREAR CLIENTE (Legacy)
            $cliente = Cliente::where('cli_ruc_ced', $request->identificacion)->first();

            if (!$cliente) {
                $cliente = Cliente::create([
                    'id_cliente'    => Cliente::generarSiguienteId(),
                    'cli_nombre'    => $request->nombre,
                    'cli_ruc_ced'   => $request->identificacion,
                    'cli_mail'      => $request->email,
                    'cli_direccion' => $request->direccion,
                    'cli_telefono'  => $request->telefono, // Campo faltante 1
                    'cli_celular'   => $request->celular, // Campo faltante 2 (opcional)
                    'id_ciudad'     => $request->id_ciudad,
                    'estado_cli'    => 'ACT'
                ]);
            }

            // 3. CREAR USUARIO (Login)
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
                'token'   => $token, // Aquí devolvemos 'token'
                'user'    => $user
            ], 201);
        });
    }
    public function login(Request $request)
    {
// Validación básica
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

// Buscamos en la tabla users por user_email
        $user = User::where('user_email', $request->email)->first();

// Verificamos password usando user_password
        if (!$user || !Hash::check($request->password, $user->user_password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

// Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Bienvenido',
            'user' => [
                'id' => $user->id_user,
                'id_cliente' => $user->id_cliente,
                'nombre' => $user->user_nombre
            ],
            'token' => $token // <--- CAMBIA 'access_token' por 'token'
        ]);
    }
}
