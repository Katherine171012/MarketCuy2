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
    @yield('styles')
</head>

@php
    $p = trim(request()->path(), '/');

    $esPortada = ($p === '' || request()->routeIs('home'));
    $esProductos = ($p === 'productos' || str_starts_with($p, 'productos/'));
    $esContacto = ($p === 'contacto' || str_starts_with($p, 'contacto/')); // por si luego creas ruta
    $esNosotros = ($p === 'nosotros' || str_starts_with($p, 'nosotros/'));

    $clasesBody = [];
    if ($esPortada)
        $clasesBody[] = 'mod-portada';
    if ($esProductos)
        $clasesBody[] = 'mod-productos';
    if ($esContacto)
        $clasesBody[] = 'mod-contacto';
    if ($esNosotros)
        $clasesBody[] = 'mod-nosotros';


    // Definimos rutas seguras (usando url() por si no tienes named routes definidas aún)
    $homeUrl = url('/');
    $productosUrl = url('/productos');
    $nosotrosUrl = route('nosotros.index');

@endphp

<body class="{{ implode(' ', $clasesBody) }}">

    {{-- ================= NAVBAR (GLOBAL) ================= --}}
    <nav class="navbar navbar-expand-lg shop-nav sticky-top py-3">
        <div class="container">
            <a class="shop-brand" href="{{ $homeUrl }}">
                <img src="{{ asset('images/logo.png') }}" alt="MarketCuy" class="brand-logo">
                <span>MarketCuy</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navShop">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navShop">
                <ul class="navbar-nav mx-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $esPortada ? 'fw-bold text-concho' : '' }}"
                            href="{{ $homeUrl }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $esProductos ? 'fw-bold text-concho' : '' }}"
                            href="{{ $productosUrl }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $esContacto ? 'fw-bold text-concho' : '' }}"
                            href="{{ route('contacto.index') }}">
                            Contacto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $esNosotros ? 'fw-bold text-concho' : '' }}" href="{{ $nosotrosUrl }}">
                            Nosotros
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
                        <a href="{{ route('cart.index') }}" class="btn btn-light border position-relative text-dark">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span id="cartCounter"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
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
                <h1 class="hero-title mb-0">Nuestros<br>Productos</h1>
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
                        <img src="{{ asset('images/logo.png') }}" alt="MarketCuy"
                            style="width:18px;height:18px;object-fit:contain;margin-right:8px;">
                        MarketCuy
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
                        <li><a href="{{ $nosotrosUrl }}">Nosotros</a></li>

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
                        <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> +593 98 341 7501</li>
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
        document.addEventListener("DOMContentLoaded", function () {
            const token = localStorage.getItem('auth_token');
            const menuAuth = document.getElementById('menuAuth');
            const menuGuest = document.getElementById('menuGuest');
            const nameSpan = document.getElementById('navUserName');
            const counterEl = document.getElementById('cartCounter');

            // --- PASO 1: CARGA INSTANTÁNEA (OPTIMISTA) ---
            // Leemos lo que guardamos la última vez para no esperar al servidor
            const cachedName = localStorage.getItem('user_name_cache');
            const cachedCart = localStorage.getItem('cart_count_cache');

            if (token) {
                if (menuGuest) menuGuest.classList.add('d-none');
                if (menuAuth) menuAuth.classList.remove('d-none');

                // Si tenemos el nombre en cache, lo ponemos YA
                if (cachedName && nameSpan) nameSpan.innerText = cachedName;
                // Si tenemos el conteo en cache, lo ponemos YA
                if (cachedCart && counterEl) counterEl.innerText = cachedCart;

                // --- PASO 2: ACTUALIZACIÓN EN SILENCIO (BACKGROUND) ---
                // Solo pedimos al servidor para verificar si algo cambió, pero el usuario ya ve sus datos

                // Pedir datos de usuario
                fetch('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(user => {
                        if (user.user_nombre) {
                            const firstName = user.user_nombre.split(' ')[0];
                            if (nameSpan) nameSpan.innerText = firstName;
                            localStorage.setItem('user_name_cache', firstName); // Actualizamos cache
                        }
                    })
                    .catch(err => { if (err.status === 401) logout(); });

                // Pedir datos del carrito
                fetch('/api/carrito', {
                    headers: { 'Authorization': `Bearer ${token}` }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.items) {
                            let totalQty = 0;
                            Object.values(data.items).forEach(item => totalQty += item.cantidad);

                            // SOLO actualizamos si el número de la API es diferente al que ya tenemos
                            // o si el usuario no ha hecho clic recientemente.
                            const counterEl = document.getElementById('cartCounter');
                            if (counterEl) {
                                counterEl.innerText = totalQty;
                                localStorage.setItem('cart_count_cache', totalQty);
                            }
                        }
                    });

            } else {
                if (menuGuest) menuGuest.classList.remove('d-none');
                if (menuAuth) menuAuth.classList.add('d-none');
            }
        });

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_name_cache'); // Limpiar cache
            localStorage.removeItem('cart_count_cache'); // Limpiar cache
            window.location.href = '/';
        }

        // ============ FUNCIÓN GLOBAL PARA ACTUALIZAR EL CONTADOR DEL CARRITO ============
        // Esta función puede ser llamada desde CUALQUIER PÁGINA después de agregar un producto
        function fetchCart() {
            const token = localStorage.getItem('auth_token');
            if (!token) return;

            fetch('/api/carrito', {
                headers: { 'Authorization': `Bearer ${token}` }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.items) {
                        let totalQty = 0;
                        Object.values(data.items).forEach(item => totalQty += item.cantidad);

                        const counterEl = document.getElementById('cartCounter');
                        if (counterEl) {
                            counterEl.innerText = totalQty;
                            localStorage.setItem('cart_count_cache', totalQty);
                        }
                    }
                })
                .catch(err => console.error('Error actualizando carrito:', err));
        }

        // Función rápida para incrementar el contador INSTANTÁNEAMENTE sin esperar al servidor
        // Esto da feedback visual inmediato al usuario
        function incrementCartCounter(cantidad = 1) {
            const counterEl = document.getElementById('cartCounter');
            if (counterEl) {
                const currentCount = parseInt(counterEl.innerText || '0', 10);
                const newCount = currentCount + cantidad;
                counterEl.innerText = newCount;
                localStorage.setItem('cart_count_cache', newCount);
            }
        }
    </script>
    @yield('scripts')
</body>

</html>