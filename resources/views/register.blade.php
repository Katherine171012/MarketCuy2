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

    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1974&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        .register-card {
            background-color: white;
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .left-panel {
            background: linear-gradient(rgba(102, 4, 4, 0.85), rgba(77, 3, 3, 0.9)), url('https://images.unsplash.com/photo-1578916171728-46686eac8d58?q=80&w=1974&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
        }

        .icon-circle {
            width: 70px; height: 70px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 20px;
            background: rgba(255,255,255,0.1);
        }

        .right-panel { padding: 40px; position: relative; }

        .close-btn {
            position: absolute; top: 15px; right: 20px;
            font-size: 24px; color: #999; text-decoration: none;
        }

        .input-group-text { background: white; border-right: none; color: #888; }
        .form-control, .form-select { border-left: none; padding: 10px; font-size: 14px; }

        /* Color Vino Focus */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control,
        .input-group:focus-within .form-select {
            border-color: #660404; color: #660404;
        }

        .btn-concho {
            background-color: #660404; color: white;
            padding: 12px; border-radius: 8px; border: none;
            font-weight: 600; width: 100%; transition: 0.3s;
        }
        .btn-concho:hover { background-color: #4d0303; color: white; }

        .link-login { color: #660404; font-weight: 600; text-decoration: none; }
        label { font-size: 0.85rem; font-weight: 600; color: #666; margin-bottom: 4px; }
    </style>
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

        <form id="registerForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Cédula / RUC</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                        <input type="text" id="identificacion" class="form-control" placeholder="1712345678" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nombre Completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" id="nombre" class="form-control" placeholder="Juan Pérez" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" id="email" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Ciudad</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
                        <select id="id_ciudad" class="form-select" required>
                            <option value="" selected disabled>Cargando ciudades...</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Teléfono Fijo (Opcional) -->
                <div class="col-md-4 mb-3">
                    <label>Teléfono Fijo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                        <input type="text" id="telefono" class="form-control" placeholder="022...">
                    </div>
                </div>
                <!-- Celular (Opcional) -->
                <div class="col-md-4 mb-3">
                    <label>Celular</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-mobile-screen"></i></span>
                        <input type="text" id="celular" class="form-control" placeholder="099...">
                    </div>
                </div>
                <!-- Dirección -->
                <div class="col-md-4 mb-3">
                    <label>Dirección</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-map-location-dot"></i></span>
                        <input type="text" id="direccion" class="form-control" placeholder="Calle...">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label>Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
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

<script>
    // 1. CARGAR CIUDADES AL INICIAR
    document.addEventListener("DOMContentLoaded", async () => {
        const selectCiudad = document.getElementById('id_ciudad');

        try {
            const response = await fetch('/api/ciudades');
            const ciudades = await response.json();

            selectCiudad.innerHTML = '<option value="" selected disabled>Selecciona tu ciudad</option>';

            ciudades.forEach(ciudad => {
                const option = document.createElement('option');
                option.value = ciudad.id_ciudad;
                option.textContent = ciudad.ciu_descripcion;
                selectCiudad.appendChild(option);
            });

        } catch (error) {
            console.error("Error cargando ciudades:", error);
            selectCiudad.innerHTML = '<option value="">Error al cargar ciudades</option>';
        }
    });

    // 2. LOGICA DE REGISTRO
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = "Registrando...";

        const payload = {
            identificacion: document.getElementById('identificacion').value,
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            id_ciudad: document.getElementById('id_ciudad').value,
            telefono: document.getElementById('telefono').value,
            celular: document.getElementById('celular').value,
            direccion: document.getElementById('direccion').value
        };

        try {
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                // 1. Guardamos el token (lo que ya tenías)
                localStorage.setItem('auth_token', data.token);

                // 2. NUEVO: Guardamos el nombre en el cache para que el Navbar sea instantáneo
                if (data.user && data.user.user_nombre) {
                    // Tomamos el nombre, sacamos el primero y lo guardamos
                    const firstName = data.user.user_nombre.split(' ')[0];
                    localStorage.setItem('user_name_cache', firstName);
                    localStorage.setItem('cart_count_cache', '0'); // Como es nuevo, empezamos en 0
                }

                // 3. Redirigimos (lo que ya tenías)
                window.location.href = '/shop';

            } else {
                // --- Todo el resto de tu código de error se queda EXACTAMENTE igual ---
                let mensaje = data.message || "Error al registrar";
                if (data.errors) {
                    mensaje = Object.values(data.errors).flat().join('<br>');
                }
                const alerta = document.getElementById('alertError');
                alerta.innerHTML = mensaje;
                alerta.classList.remove('d-none');
            }
        } catch (error) {
            console.error(error);
            alert("Error de conexión con el servidor");
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
</script>
</body>
</html>
