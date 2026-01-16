<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContactoController extends Controller
{
    public function index()
    {
        // Solo muestra la vista
        return view('contacto.index');
    }

    public function store(Request $request)
    {
        // Validación (server-side)
        $data = $request->validate([
            'con_nombre'   => 'required|string|min:3|max:100',
            'con_correo'   => 'required|email|max:150',
            'con_telefono' => 'nullable|string|max:20',
            'con_tipo'     => 'required|in:productos,pedidos,pagos,sugerencias',
            'con_mensaje'  => 'required|string|min:10|max:500',
        ]);

        // Generar referencia tipo: MC-2026-XXXXXX
        $ref = 'MC-' . now()->format('Y') . '-' . strtoupper(Str::random(6));

        // Log dedicado: storage/logs/contacto.log
        $canal = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/contacto.log'),
            'level' => 'info',
        ]);

        $canal->info('Nuevo contacto recibido', [
            'ref' => $ref,
            'nombre' => $data['con_nombre'],
            'correo' => $data['con_correo'],
            'telefono' => $data['con_telefono'] ?? null,
            'tipo' => $data['con_tipo'],
            'mensaje' => $data['con_mensaje'],
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 255),
            'fecha' => now()->toDateTimeString(),
        ]);

        return redirect()
            ->route('contacto.index')
            ->with('contacto_ok', true)
            ->with('contacto_ref', $ref)
            ->with('contacto_nombre', $data['con_nombre']);
    }
}
