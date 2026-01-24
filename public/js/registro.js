/* public/js/registro.js */

// ---------------------------------------------
// 1. FUNCIONES DE VALIDACIÓN (Lógica)
// ---------------------------------------------

function validarEmail() {
    const inputEmail = document.getElementById('email');
    const correo = inputEmail.value;
    // Regex estricto: Texto + @ + Texto + . + Texto
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

function validarPassword() {
    const password = document.getElementById('password').value;
    const barra = document.getElementById('passwordStrength');
    const help = document.getElementById('passwordHelp');

    let fuerza = 0;

    // Reglas de fuerza
    if (password.length >= 8) fuerza++; // Longitud
    if (/[A-Z]/.test(password)) fuerza++; // Mayúscula
    if (/[0-9]/.test(password)) fuerza++; // Número
    if (/[^A-Za-z0-9]/.test(password)) fuerza++; // Símbolo especial

    // Actualizar barra visualmente
    barra.className = 'progress-bar'; // Reset clases
    if (fuerza <= 1) {
        barra.style.width = '25%';
        barra.classList.add('bg-danger');
        if(help) help.innerText = "Débil: Usa más caracteres y números.";
    } else if (fuerza === 2 || fuerza === 3) {
        barra.style.width = '50%';
        barra.classList.add('bg-warning');
        if(help) help.innerText = "Normal: Agrega un símbolo o mayúscula.";
    } else {
        barra.style.width = '100%';
        barra.classList.add('bg-success');
        if(help) help.innerText = "¡Contraseña Fuerte!";
    }

    // Revalidar coincidencia si cambia la contraseña original
    validarCoincidencia();
}

function validarCoincidencia() {
    const pass1 = document.getElementById('password');
    const pass2 = document.getElementById('password_confirmation');

    // Si el campo de confirmación no existe aún en el DOM (por si acaso), retornamos true para no romper
    if (!pass2) return true;

    if (pass2.value === "") return true; // No marcar error si está vacío todavía

    if (pass1.value !== pass2.value) {
        pass2.classList.add('is-invalid');
        pass2.classList.remove('is-valid');
        return false;
    } else {
        pass2.classList.remove('is-invalid');
        pass2.classList.add('is-valid');
        return true;
    }
}

// ---------------------------------------------
// 2. CARGAR CIUDADES AL INICIAR
// ---------------------------------------------
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

// ---------------------------------------------
// 3. LOGICA DE REGISTRO (Submit)
// ---------------------------------------------
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    // A. Validar Email
    if (!validarEmail()) {
        document.getElementById('email').focus();
        return;
    }

    // B. Validar Coincidencia de Contraseñas (NUEVO)
    if (!validarCoincidencia()) {
        document.getElementById('password_confirmation').focus();
        return;
    }

    // C. Preparar Botón
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = "Registrando...";

    document.getElementById('alertError').classList.add('d-none');

    // D. Crear Payload (AQUÍ AGREGAMOS LA CONFIRMACIÓN)
    const payload = {
        identificacion: document.getElementById('identificacion').value,
        nombre: document.getElementById('nombre').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value, // <--- SE AGREGA ESTO
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
