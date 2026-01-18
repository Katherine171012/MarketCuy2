<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'MarketCuy')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- CSS GLOBAL (para todo: home, productos, contacto, etc.) --}}
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
</head>

@php
    $p = trim(request()->path(), '/');

    $esPortada     = ($p === '' || request()->routeIs('home'));
    $esProductos   = ($p === 'productos' || str_starts_with($p, 'productos/'));
    $esContacto    = ($p === 'contacto' || str_starts_with($p, 'contacto/')); // por si luego creas ruta

    $clasesBody = [];
    if ($esPortada)   $clasesBody[] = 'mod-portada';
    if ($esProductos) $clasesBody[] = 'mod-productos';
    if ($esContacto)  $clasesBody[] = 'mod-contacto';

    $homeUrl = route('home');
    $productosUrl = route('productos.index');
@endphp

<body class="{{ implode(' ', $clasesBody) }}">

{{-- ================= NAVBAR (GLOBAL) ================= --}}
<nav class="navbar navbar-expand-lg shop-nav sticky-top py-3">
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
                    <a class="nav-link {{ $esPortada ? 'fw-bold text-concho' : '' }}" href="{{ $homeUrl }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $esProductos ? 'fw-bold text-concho' : '' }}" href="{{ $productosUrl }}">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $esContacto ? 'fw-bold text-concho' : '' }}"
                       href="{{ route('contacto.index') }}">
                        Contacto
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border" type="button" disabled title="Carrito (demo)">
                    <i class="fa-solid fa-cart-shopping"></i>
                </button>
                <button class="btn btn-concho px-4" type="button" disabled>Iniciar sesión</button>
            </div>
        </div>
    </div>
</nav>

{{-- ================= HERO SOLO PRODUCTOS ================= --}}
@if($esProductos)
    <div class="shop-hero">
        <div class="container">
            <h1 class="display-6 mb-0">Nuestros Productos</h1>
        </div>
    </div>
@endif

{{-- ================= CONTENIDO ================= --}}
<main class="{{ $esProductos ? 'shop-shell' : '' }}">
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
                    <li><a href="{{ route('contacto.index') }}">Contacto</a></li>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
