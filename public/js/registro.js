// 1. FUNCIÓN PARA VALIDAR EMAIL
function validarEmail() {
    const inputEmail = document.getElementById('email');
    const correo = inputEmail.value;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (regex.test(correo)) {
        inputEmail.classList.remove('is-invalid');
        inputEmail.classList.add('is-valid');
        return true;
    } else {
        inputEmail.classList.remove('is-valid');
        inputEmail.classList.add('is-invalid');
        return false;
    }
}

// 2. CARGAR CIUDADES AL INICIAR
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

// 3. LOGICA DE REGISTRO
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validarEmail()) {
        document.getElementById('email').focus();
        return;
    }

    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = "Registrando...";

    document.getElementById('alertError').classList.add('d-none');

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
            localStorage.setItem('auth_token', data.token);

            if (data.user && data.user.user_nombre) {
                const firstName = data.user.user_nombre.split(' ')[0];
                localStorage.setItem('user_name_cache', firstName);
                localStorage.setItem('cart_count_cache', '0');
            }

            window.location.href = '/shop';

        } else {
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
        const alerta = document.getElementById('alertError');
        alerta.innerText = "Error de conexión con el servidor";
        alerta.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerText = originalText;
    }
});
