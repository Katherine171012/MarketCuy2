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

    {{-- CSS GLOBAL --}}
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
</head>

@php
    $p = trim(request()->path(), '/');

    $esClientes     = ($p === 'clientes' || str_starts_with($p, 'clientes/'));
    $esProductos    = ($p === 'productos' || str_starts_with($p, 'productos/'));
    $esProveedores  = ($p === 'proveedores' || str_starts_with($p, 'proveedores/'));
    $esPortada      = ($p === '' || request()->routeIs('home'));
    $esContacto     = ($p === 'contacto' || str_starts_with($p, 'contacto/')); // por si luego creas ruta

    $clasesBody = [];
    if ($esClientes)     $clasesBody[] = 'mod-clientes';
    if ($esPortada)      $clasesBody[] = 'mod-portada';
    if ($esProductos)    $clasesBody[] = 'mod-productos';
    if ($esProveedores)  $clasesBody[] = 'mod-proveedores';
    if ($esContacto)     $clasesBody[] = 'mod-contacto';

    // Home URL (seguro)
    $homeUrl = '/';
    try {
        if (function_exists('route') && app('router')->has('home')) {
            $homeUrl = route('home');
        }
    } catch (\Throwable $e) {
        $homeUrl = '/';
    }

    // Productos URL (seguro)
    $productosUrl = '#';
    try {
        if (function_exists('route') && app('router')->has('productos.index')) {
            $productosUrl = route('productos.index');
        }
    } catch (\Throwable $e) {
        $productosUrl = '#';
    }
@endphp

<body class="{{ implode(' ', $clasesBody) }}">

{{-- NAV: si es Productos -> estilo tienda; caso contrario -> nav simple --}}
@if($esProductos)
    <nav class="navbar navbar-expand-lg shop-nav sticky-top py-3">
        <div class="container">
            <a class="shop-brand" href="{{ $homeUrl }}">
                <span class="logo"><i class="fa-solid fa-cart-shopping"></i></span>
                <span>MarketCuy</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navShop" aria-controls="navShop" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navShop">
                <ul class="navbar-nav mx-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $esPortada ? 'fw-bold text-concho' : '' }}" href="{{ $homeUrl }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $esProductos ? 'fw-bold text-concho' : '' }}" href="{{ $productosUrl }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $esContacto ? 'fw-bold text-concho' : '' }}" href="#" onclick="return false;">Contacto</a>
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

    {{-- HERO SOLO PRODUCTOS --}}
    <div class="shop-hero">
        <div class="container">
            <h1 class="display-6 mb-0">Nuestros Productos</h1>
        </div>
    </div>
@else
    <nav class="navbar navbar-expand-lg navbar-dark bg-concho">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ $homeUrl }}">MarketCuy</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto">
                    @if($esClientes)
                        <li class="nav-item"><span class="nav-link active fw-semibold">Clientes</span></li>
                    @elseif($esProveedores)
                        <li class="nav-item"><span class="nav-link active fw-semibold">Proveedores</span></li>
                    @elseif($esProductos)
                        <li class="nav-item"><span class="nav-link active fw-semibold">Productos</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif

{{-- ================= CONTENIDO ================= --}}
<main class="{{ $esProductos ? 'shop-shell' : 'container py-4' }}">

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

<footer class="footer-main py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-cart-shopping me-2"></i> MarketCuy
                </h5>
                <p class="small text-muted">
                    Tu supermercado en casa. Productos frescos y de calidad directo a tu puerta.
                </p>
            </div>

            <div class="col-6 col-md-2">
                <h6 class="fw-bold mb-3">Enlaces</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="{{ $homeUrl }}">Inicio</a></li>
                    <li><a href="{{ $productosUrl }}">Productos</a></li>
                    <li><a href="#" onclick="return false;">Contacto</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3">
                <h6 class="fw-bold mb-3">Categorías</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="#" onclick="return false;">Frutas y Verduras</a></li>
                    <li><a href="#" onclick="return false;">Carnes</a></li>
                    <li><a href="#" onclick="return false;">Lácteos</a></li>
                    <li><a href="#" onclick="return false;">Panadería</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-3">
                <h6 class="fw-bold mb-3">Contacto</h6>
                <ul class="list-unstyled small footer-contact">
                    <li class="mb-2"><i class="fa-regular fa-envelope me-2"></i> contacto@marketcuy.com</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> +593 99 999 9999</li>
                    <li><i class="fa-solid fa-location-dot me-2"></i> Quito, Ecuador</li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="text-center small footer-copy">
            © {{ date('Y') }} MarketCuy. Todos los derechos reservados.
        </div>
    </div>
</footer>

{{-- Scripts específicos de cada módulo --}}
@yield('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
