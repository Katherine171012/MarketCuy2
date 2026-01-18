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

    // Definimos rutas seguras (usando url() por si no tienes named routes definidas aún)
    $homeUrl = url('/');
    $productosUrl = url('/productos');
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

                {{-- A) PARA INVITADOS (NO LOGUEADOS) --}}
                <div id="menuGuest" class="d-flex align-items-center gap-3">
                    <a href="{{ url('/login') }}" class="btn btn-concho px-4">Iniciar sesión</a>
                </div>

                {{-- B) PARA USUARIOS (LOGUEADOS) --}}
                <div id="menuAuth" class="d-none align-items-center gap-3">

                    {{-- Botón Carrito con Contador Real --}}
                    <a href="{{ url('/cart/view') }}" class="btn btn-light border position-relative text-dark">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cartCounter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>

                    <div class="vr mx-2"></div> {{-- Línea separadora vertical --}}

                    <span class="small fw-bold text-secondary">
                        Hola, <span id="navUserName">Usuario</span>
                    </span>

                    <button onclick="logout()" class="btn btn-outline-danger btn-sm" title="Cerrar Sesión">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>

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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Verificar si existe el Token
        const token = localStorage.getItem('auth_token');
        const menuAuth = document.getElementById('menuAuth');
        const menuGuest = document.getElementById('menuGuest');

        if (token) {
            // -- USUARIO LOGUEADO --
            if(menuGuest) menuGuest.classList.add('d-none'); // Ocultar Login
            if(menuAuth) menuAuth.classList.remove('d-none'); // Mostrar Menú Usuario

            // Cargar datos del usuario (Nombre)
            fetch('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(user => {
                    const nameSpan = document.getElementById('navUserName');
                    if(nameSpan && user.user_nombre) {
                        // Tomar solo el primer nombre
                        nameSpan.innerText = user.user_nombre.split(' ')[0];
                    }
                })
                .catch(() => logout()); // Si falla (token expirado), sacar al usuario

            // Cargar Contador del Carrito (Desde Firebase vía Laravel)
            fetch('/api/cart/data', {
                headers: { 'Authorization': `Bearer ${token}` }
            })
                .then(res => res.json())
                .then(data => {
                    const counterEl = document.getElementById('cartCounter');
                    if(counterEl && data.items) {
                        let totalQty = 0;
                        // Sumar las cantidades de todos los items
                        Object.values(data.items).forEach(item => totalQty += item.cantidad);
                        counterEl.innerText = totalQty;
                    }
                });

        } else {
            // -- USUARIO INVITADO --
            if(menuGuest) menuGuest.classList.remove('d-none');
            if(menuAuth) menuAuth.classList.add('d-none');
        }
    });

    // Función Global de Logout
    function logout() {
        localStorage.removeItem('auth_token');
        window.location.href = '/'; // Redirigir al home/login
    }
</script>
@yield('scripts')
</body>
</html>
