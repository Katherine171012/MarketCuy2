<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo', 'MarketCuy')</title>

    {{-- Bootstrap (unificado) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- CSS organizado (IMPORTANTE: este archivo debe existir en public/css también o compilarse con Vite) --}}
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
</head>

@php
    $p = trim(request()->path(), '/');

    $esClientes     = ($p === 'clientes' || str_starts_with($p, 'clientes/'));
    $esProductos    = ($p === 'productos' || str_starts_with($p, 'productos/'));
    $esProveedores  = ($p === 'proveedores' || str_starts_with($p, 'proveedores/'));

    $clasesBody = [];
    if ($esClientes) $clasesBody[] = 'mod-clientes';
    if ($esProductos) $clasesBody[] = 'mod-productos';
    if ($esProveedores) $clasesBody[] = 'mod-proveedores';

    $homeUrl = '/';
    try {
        if (function_exists('route') && app('router')->has('home')) {
            $homeUrl = route('home');
        }
    } catch (\Throwable $e) {
        $homeUrl = '/';
    }
@endphp

<body class="{{ implode(' ', $clasesBody) }}">

{{-- NAV: si es Productos -> estilo tienda; caso contrario -> nav simple --}}
@if($esProductos)
    <nav class="navbar navbar-expand-lg shop-nav py-3">
        <div class="container">
            <a class="shop-brand" href="{{ $homeUrl }}">
                <span class="logo"><i class="fa-solid fa-cart-shopping"></i></span>
                <span>MarketCuy</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navShop">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navShop">
                <ul class="navbar-nav mx-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'fw-bold text-concho' : '' }}" href="{{ $homeUrl }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-concho" href="{{ route('productos.index') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="return false;">Contacto</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light border" type="button" disabled title="Carrito (demo)">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </button>

                    <button class="btn btn-concho px-4" type="button" disabled>
                        Iniciar sesión
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="shop-hero">
        <div class="container">
            <h1 class="display-6 mb-0">Nuestros Productos</h1>
        </div>
    </div>
@else
    <nav class="navbar navbar-expand-lg navbar-dark bg-concho">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ $homeUrl }}">MarketCuy</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto">
                    @if($esClientes)
                        <li class="nav-item"><span class="nav-link active fw-semibold">Clientes</span></li>
                    @elseif($esProductos)
                        <li class="nav-item"><span class="nav-link active fw-semibold">Productos</span></li>
                    @elseif($esProveedores)
                        <li class="nav-item"><span class="nav-link active fw-semibold">Proveedores</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif

<main class="container @if($esProductos) shop-shell @else py-4 @endif">

    {{-- MENSAJES (tu lógica actual) --}}
    @if(session('codigo_mensaje'))
        @php
            $tipo = session('tipo_mensaje', 'success');
            $texto = config('mensajes.' . session('codigo_mensaje'));
        @endphp

        @if($texto)
            <div class="alert alert-{{ $tipo }} alert-soft py-2 small fw-bold mb-3">
                {{ $texto }}
            </div>
        @endif
    @endif

    {{-- Compatibilidad mensajes viejos --}}
    @if(session('ok'))
        <div class="alert alert-success alert-soft">{{ session('ok') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-soft">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-soft">{{ session('error') }}</div>
    @endif

    @yield('contenido')
    @yield('content')

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
