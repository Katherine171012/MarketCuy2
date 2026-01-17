<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ecommerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card p-4 shadow" style="width: 400px;">
    <h3 class="text-center mb-4">Iniciar Sesión</h3>

    <!-- Mensaje de Error -->
    <div id="alertError" class="alert alert-danger d-none"></div>

    <form id="loginForm">
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contraseña:</label>
            <input type="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
    <p class="mt-3 text-center">
        ¿No tienes cuenta? <a href="/registro">Regístrate aquí</a>
    </p>
</div>

<script>
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // Evitar recarga normal

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            // 1. Petición a TU API
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
                // 2. ÉXITO: Guardamos el Token en LocalStorage
                localStorage.setItem('auth_token', data.token);

                // Redirigimos al dashboard
                window.location.href = '/dashboard';
            } else {
                // ERROR
                const alerta = document.getElementById('alertError');
                alerta.innerText = data.message || 'Error en credenciales';
                alerta.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
</script>
</body>
</html>
