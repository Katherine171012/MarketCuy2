<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Obtener lista de ciudades para el formulario de registro
     */
    public function getCiudades()
    {
        // Asumiendo que la tabla ciudades tiene 'ciu_descripcion' o 'nombre'
        // Ajusta el 'orderBy' según tu tabla Ciudad
        $ciudades = Ciudad::orderBy('id_ciudad', 'asc')->get();
        return response()->json($ciudades);
    }

    /**
     * Registro de usuarios (Crea Cliente + User)
     */
    public function register(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            'identificacion' => 'required|string|max:13',
            'nombre'         => 'required|string|max:255',
            'id_ciudad'      => 'required|exists:ciudades,id_ciudad',
            'direccion'      => 'nullable|string',
            'telefono'       => 'nullable|string',
            'celular'        => 'nullable|string',
            'password'       => 'required|string|min:8',
            // VALIDACIÓN DEL EMAIL
            'email'          => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,user_email', // Verifica que no exista en la columna user_email
                'regex:/^.+@.+\..+$/i'     // <--- TU REGLA: Requiere @ y un punto con extensión
            ],
        ], [
            'email.regex'    => 'El correo debe tener un formato válido (ejemplo: nombre@dominio.com)',
            'email.unique'   => 'Este correo ya está registrado.',
            'password.min'   => 'La contraseña debe tener al menos 8 caracteres.'
        ]);

        // 2. Transacción en Base de Datos
        return DB::transaction(function () use ($request) {

            // A. Buscar si el cliente ya existe (por RUC/Cédula) para no duplicarlo
            $cliente = Cliente::where('cli_ruc_ced', $request->identificacion)->first();

            // B. Si NO existe, creamos el Cliente (Perfil de Negocio)
            if (!$cliente) {
                $cliente = Cliente::create([
                    'id_cliente'    => Cliente::generarSiguienteId(), // Tu método estático del modelo
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

            // C. Crear el Usuario (Credenciales de Acceso)
            $user = User::create([
                'id_cliente'    => $cliente->id_cliente, // Vinculación FK
                'user_nombre'   => $request->nombre,
                'user_email'    => $request->email,
                'user_password' => Hash::make($request->password), // Encriptación
                'user_rol'      => 'customer',
                'estado_user'   => 'ACT'
            ]);

            // D. Generar Token de acceso inmediato
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

    /**
     * Login de usuarios
     */
    public function login(Request $request)
    {
        // 1. Validar inputs
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // 2. Buscar usuario por su email personalizado
        $user = User::where('user_email', $request->email)->first();

        // 3. Verificar contraseña encriptada
        if (!$user || !Hash::check($request->password, $user->user_password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // 4. Verificar estado (Opcional)
        if ($user->estado_user !== 'ACT') {
            return response()->json([
                'message' => 'Su cuenta se encuentra inactiva. Contacte al administrador.'
            ], 403);
        }

        // 5. Generar Token (Elimina tokens anteriores si quieres sesión única)
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

    /**
     * Logout (Cerrar sesión / Revocar token)
     */
    public function logout(Request $request)
    {
        // Revoca el token actual que se usó para la petición
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
