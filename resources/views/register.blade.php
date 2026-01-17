<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="card p-4 shadow" style="width: 500px;">
    <h3 class="text-center mb-4">Crear Cuenta</h3>
    <div id="alertError" class="alert alert-danger d-none"></div>

    <form id="registerForm">
        <div class="row">
            <div class="col-6 mb-3">
                <label>Cédula/RUC:</label>
                <input type="text" id="identificacion" class="form-control" required>
            </div>
            <div class="col-6 mb-3">
                <label>Nombre:</label>
                <input type="text" id="nombre" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input type="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contraseña:</label>
            <input type="password" id="password" class="form-control" required>
        </div>

        <!-- Campo Ciudad (Debe existir en tu BD) -->
        <div class="mb-3">
            <label>Código de Ciudad (Ej: UIO):</label>
            <input type="text" id="id_ciudad" class="form-control" placeholder="UIO" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Registrarse</button>
    </form>
    <p class="mt-3 text-center">
        <a href="/">Volver al Login</a>
    </p>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        // Recolectar datos
        const payload = {
            identificacion: document.getElementById('identificacion').value,
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            id_ciudad: document.getElementById('id_ciudad').value
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
                alert('Registro Exitoso. Ahora inicia sesión.');
                window.location.href = '/';
            } else {
                // Mostrar errores de validación de Laravel
                let mensaje = data.message;
                if(data.errors) {
                    mensaje = Object.values(data.errors).flat().join('\n');
                }
                const alerta = document.getElementById('alertError');
                alerta.innerText = mensaje;
                alerta.classList.remove('d-none');
            }
        } catch (error) {
            console.error(error);
        }
    });
</script>
</body>
</html>
