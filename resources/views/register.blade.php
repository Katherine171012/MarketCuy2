<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - MarketCuy</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- TU CSS PERSONALIZADO -->
    <link rel="stylesheet" href="{{ asset('css/registro.css') }}">
</head>
<body>

<div class="register-card row g-0">
    <!-- LADO IZQUIERDO -->
    <div class="col-md-4 left-panel d-none d-md-flex">
        <div class="icon-circle"><i class="fa-solid fa-user-plus"></i></div>
        <h3 class="fw-bold">Únete a nosotros</h3>
        <p class="mt-2 opacity-75 small">Crea tu cuenta para acceder a las mejores ofertas.</p>
    </div>

    <!-- LADO DERECHO (Formulario) -->
    <div class="col-md-8 right-panel">
        <a href="/" class="close-btn">&times;</a>

        <h3 class="fw-bold mb-1" style="color: #660404;">Crear Cuenta</h3>
        <p class="text-muted mb-4 small">Completa tus datos para registrarte</p>

        <!-- Mensajes de Error -->
        <div id="alertError" class="alert alert-danger d-none small"></div>

        <form id="registerForm" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Cédula / RUC * </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                        <input type="text" id="identificacion" class="form-control" placeholder="1712345678" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nombre Completo *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" id="nombre" class="form-control" placeholder="Juan Pérez" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Correo Electrónico * </label>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" id="email" class="form-control" placeholder="correo@ejemplo.com" required oninput="validarEmail()">
                        <div id="emailFeedback" class="invalid-feedback">
                            El correo debe tener un @ y una extensión válida.
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Ciudad</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
                        <select id="id_ciudad" class="form-select" required>
                            <option value="" selected disabled>Ciudades</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Teléfono Fijo (Opcional) </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                        <input type="text" id="telefono" class="form-control" placeholder="022...">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Celular * </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-mobile-screen"></i></span>
                        <input type="text" id="celular" class="form-control" placeholder="099...">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Dirección * </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-map-location-dot"></i></span>
                        <input type="text" id="direccion" class="form-control" placeholder="Calle...">
                    </div>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label>Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <!-- Agregamos onkeyup para validar fuerza en tiempo real -->
                    <input type="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required onkeyup="validarPassword()">
                </div>
                <!-- Barra de fuerza -->
                <div class="progress mt-1" style="height: 5px;">
                    <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small id="passwordHelp" class="text-muted" style="font-size: 0.75rem;">Debe tener números y mayúsculas.</small>
            </div>
            <div class="mb-4">
                <label>Confirmar Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <!-- Agregamos onkeyup para verificar coincidencia -->
                    <input type="password" id="password_confirmation" class="form-control" placeholder="Repite tu contraseña" required onkeyup="validarCoincidencia()">
                    <div id="matchFeedback" class="invalid-feedback">
                        Las contraseñas no coinciden.
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-concho mb-3" id="btnRegistro">
                Registrarse
            </button>

            <div class="text-center small">
                ¿Ya tienes una cuenta? <a href="/login" class="link-login">Inicia Sesión aquí</a>
            </div>
        </form>
    </div>
</div>

<!-- TU JS PERSONALIZADO -->
<script src="{{ asset('js/registro.js') }}"></script>

</body>
</html>
