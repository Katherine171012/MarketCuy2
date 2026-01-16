<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;

class FirebaseTestController extends Controller
{
    public function test(FirebaseService $firebase)
    {
        $db = $firebase->database();

        $db->getReference('test/conexion')->set([
            'mensaje' => 'Laravel conectado a Realtime Database',
            'fecha' => now()->toDateTimeString()
        ]);

        return response()->json([
            'ok' => true,
            'mensaje' => 'Conexión exitosa con Realtime Database'
        ]);
    }
}
