<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MarketCuy</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* ESTILOS ESPECÍFICOS SOLO PARA EL LOGIN */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1974&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background-color: white;
            border-radius: 20px;
            overflow: hidden;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            min-height: 550px;
        }

        /* Panel Izquierdo (Imagen y Color Vino) */
        .left-panel {
            /* Degradado color Concho/Vino sobre la imagen */
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
            width: 80px; height: 80px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; margin-bottom: 20px;
            background: rgba(255,255,255,0.1);
        }

        /* Panel Derecho (Formulario) */
        .right-panel {
            padding: 50px;
            display: flex; flex-direction: column; justify-content: center;
            position: relative;
        }

        .close-btn {
            position: absolute; top: 20px; right: 25px;
            font-size: 24px; color: #999; text-decoration: none;
        }
        .close-btn:hover { color: #333; }

        /* Inputs Estilizados */
        .input-group-text { background: white; border-right: none; color: #888; }
        .form-control { border-left: none; padding: 12px; font-size: 15px; }
        .form-control:focus { box-shadow: none; border-color: #ced4da; }

        /* Focus color vino */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #660404;
            color: #660404;
        }

        /* Botones */
        .btn-concho {
            background-color: #660404; color: white;
            padding: 12px; border-radius: 8px; border: none;
            font-weight: 600; width: 100%; transition: 0.3s;
        }
        .btn-concho:hover { background-color: #4d0303; color: white; }

        .btn-outline {
            border: 1px solid #ddd; color: #555;
            padding: 10px; border-radius: 8px; width: 100%;
            display: block; text-align: center; text-decoration: none;
            font-weight: 600; font-size: 14px;
        }
        .btn-outline:hover { background-color: #f9f9f9; color: #000; }
    </style>
</head>
<body>

<div class="login-card row g-0">

    <!-- LADO IZQUIERDO (Visual) -->
    <div class="col-md-5 left-panel d-none d-md-flex">
        <div class="icon-circle">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <h2 class="fw-bold">Bienvenido a MarketCuy</h2>
        <p class="mt-2 opacity-75">Tu supermercado premium en casa.</p>
    </div>

    <!-- LADO DERECHO (Formulario) -->
    <div class="col-md-7 right-panel">
        <a href="/" class="close-btn">&times;</a>

        <h3 class="fw-bold mb-1" style="color: #660404;">Iniciar Sesión</h3>
        <p class="text-muted mb-4 small">Ingresa tus credenciales para continuar</p>

        <!-- Alerta de Error (Funcionalidad Original) -->
        <div id="alertError" class="alert alert-danger d-none small"></div>

        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                    <!-- ID email (Crucial para tu JS) -->
                    <input type="email" id="email" class="form-control" placeholder="nombre@correo.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <!-- ID password (Crucial para tu JS) -->
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-concho mb-3">
                Ingresar
            </button>

            <div class="text-center text-muted small mb-2">o</div>

            <a href="/registro" class="btn btn-outline">
                ¿No tienes cuenta? Regístrate aquí
            </a>
        </form>
    </div>
</div>
<script>
    const form = document.getElementById('loginForm');
    const alerta = document.getElementById('alertError');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // UI: Bloquear botón y limpiar errores
        alerta.classList.add('d-none');
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = "Verificando...";

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok) {
                // 1. Guardar Token
                localStorage.setItem('auth_token', data.token);

                // 2. Guardar Nombre (con validación para que no se rompa el JS)
                if (data.user && data.user.user_nombre) {
                    const firstName = data.user.user_nombre.split(' ')[0];
                    localStorage.setItem('user_name_cache', firstName);
                } else {
                    localStorage.setItem('user_name_cache', 'Usuario');
                }

                // 3. Redirigir
                window.location.href = '/shop';

            } else {
                // 4. Mostrar error si las credenciales fallan
                alerta.innerText = data.message || 'Correo o contraseña incorrectos';
                alerta.classList.remove('d-none');
                btn.disabled = false;
                btn.innerText = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alerta.innerText = 'Error de conexión con el servidor';
            alerta.classList.remove('d-none');
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
</script>
</body>
</html>
