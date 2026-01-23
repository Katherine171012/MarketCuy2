<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;

class CiudadController extends Controller
{
    public function index()
    {
        return response()->json(
            Ciudad::orderBy('ciu_descripcion')->get()
        );
    }
}
