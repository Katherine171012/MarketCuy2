<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar Sesión - MarketCuy</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-card row g-0">

    <div class="col-md-5 left-panel d-none d-md-flex">
        <div class="icon-circle">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <h2 class="fw-bold">Bienvenido a MarketCuy</h2>
        <p class="mt-2 opacity-75">Tu supermercado premium en casa.</p>
    </div>


    <div class="col-md-7 right-panel">
        <a href="/" class="close-btn">&times;</a>

        <h3 class="fw-bold mb-1" style="color: #660404;">Iniciar Sesión</h3>
        <p class="text-muted mb-4 small">Ingresa tus credenciales para continuar</p>

        <div id="alertError" class="alert alert-danger d-none small"></div>

        <form id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" id="email" class="form-control" placeholder="nombre@correo.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" class="form-control with-eye" placeholder="••••••••" required>
                    <span class="input-group-text toggle-pass" id="btnToggle">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-concho mb-3">
                Ingresar
            </button>

            <div class="text-center text-muted small mb-2">o</div>

            <a href="{{ url('/registro') }}" class="btn btn-outline">
                ¿No tienes cuenta? Regístrate aquí
            </a>
        </form>
    </div>
</div>


<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
