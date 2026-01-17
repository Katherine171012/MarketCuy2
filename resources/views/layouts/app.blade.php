<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo', 'MarketCuy')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- CSS organizado --}}
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
</head>

{{-- Forzamos la clase 'mod-productos' para que el CSS aplique los estilos blancos --}}
<body class="mod-productos">

{{-- NAVBAR ESTÁTICO (Igual para todo el sitio) --}}
<nav class="navbar navbar-expand-lg shop-nav py-3 sticky-top">
    <div class="container">
        {{-- Logo --}}
        <a class="shop-brand" href="{{ url('/') }}">
            <span class="logo"><i class="fa-solid fa-cart-shopping"></i></span>
            <span>MarketCuy</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navShop">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navShop">
            {{-- Enlaces --}}
            <ul class="navbar-nav mx-auto gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'fw-bold text-concho' : '' }}" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    {{-- Asumiendo que tu ruta de productos se llama 'productos.index' --}}
                    <a class="nav-link {{ request()->is('productos*') ? 'fw-bold text-concho' : '' }}" href="{{ route('productos.index') }}">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="return false;">Contacto</a>
                </li>
            </ul>

            {{-- Iconos derecha (Carrito y Login) --}}
            <div class="d-flex align-items-center gap-3">

                {{-- AQUÍ ESTÁ EL CARRITO TAL CUAL TU CÓDIGO --}}
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

{{-- CONTENIDO PRINCIPAL --}}
<main class="shop-shell">
    <div class="container">
        {{-- MENSAJES DE SESIÓN --}}
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
            <div class="alert alert-success alert-soft mb-3">{{ session('ok') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-soft mb-3">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-soft mb-3">{{ session('error') }}</div>
        @endif
    </div>

    {{-- Aquí se carga la vista (Home o Productos) --}}
    @yield('contenido')
    @yield('content')

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
