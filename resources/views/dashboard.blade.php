<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
<h1>Bienvenido, <span id="userName">Cargando...</span></h1>
<p>ID Cliente: <span id="userId">...</span></p>
<button class="btn btn-danger" onclick="logout()">Cerrar Sesión</button>

<script>
    // 1. Verificar si hay token
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/';
    }

    // 2. Cargar datos del usuario
    async function loadUser() {
        const response = await fetch('/api/user', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`, // ENVIAMOS EL TOKEN
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const user = await response.json();
            document.getElementById('userName').innerText = user.user_nombre;
            document.getElementById('userId').innerText = user.id_cliente;
        } else {
            logout(); // Si el token venció, salir
        }
    }

    // 3. Función Logout
    function logout() {
        localStorage.removeItem('auth_token');
        window.location.href = '/';
    }

    loadUser();
</script>
</body>
</html>
