// 1. FUNCIONES GLOBALES (Para que funcionen con el 'onkeyup' del HTML)
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

function validarPassword() {
    const password = document.getElementById('password').value;
    const barra = document.getElementById('passwordStrength');
    const help = document.getElementById('passwordHelp');
    let fuerza = 0;

    if (password.length >= 8) fuerza++;
    if (/[A-Z]/.test(password)) fuerza++;
    if (/[0-9]/.test(password)) fuerza++;
    if (/[^A-Za-z0-9]/.test(password)) fuerza++;

    barra.className = 'progress-bar';
    if (fuerza <= 1) {
        barra.style.width = '25%';
        barra.classList.add('bg-danger');
        if(help) help.innerText = "Débil: Usa más letras y números.";
    } else if (fuerza === 2 || fuerza === 3) {
        barra.style.width = '50%';
        barra.classList.add('bg-warning');
        if(help) help.innerText = "Normal: Agrega un símbolo y una mayúscula.";
    } else {
        barra.style.width = '100%';
        barra.classList.add('bg-success');
        if(help) help.innerText = "¡Contraseña Fuerte!";
    }
    validarCoincidencia();
}

function validarCoincidencia() {
    const pass1 = document.getElementById('password');
    const pass2 = document.getElementById('password_confirmation');
    if (!pass2 || pass2.value === "") return true;

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

// 2. INICIALIZACIÓN CUANDO CARGA EL DOM
document.addEventListener("DOMContentLoaded", async () => {
    console.log("Registro JS cargado correctamente");

    // --- LÓGICA DEL OJITO 1 ---
    const btnToggle1 = document.getElementById('btnToggle1');
    const passwordInput1 = document.getElementById('password');
    const eyeIcon1 = document.getElementById('eyeIcon1');

    if (btnToggle1 && passwordInput1) {
        btnToggle1.addEventListener('click', function() {
            const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput1.setAttribute('type', type);
            eyeIcon1.classList.toggle('fa-eye');
            eyeIcon1.classList.toggle('fa-eye-slash');
        });
    }

    // --- LÓGICA DEL OJITO 2 ---
    const btnToggle2 = document.getElementById('btnToggle2');
    const passwordInput2 = document.getElementById('password_confirmation');
    const eyeIcon2 = document.getElementById('eyeIcon2');

    if (btnToggle2 && passwordInput2) {
        btnToggle2.addEventListener('click', function() {
            const type = passwordInput2.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput2.setAttribute('type', type);
            eyeIcon2.classList.toggle('fa-eye');
            eyeIcon2.classList.toggle('fa-eye-slash');
        });
    }

    // --- CARGAR CIUDADES ---
    const selectCiudad = document.getElementById('id_ciudad');
    if (selectCiudad) {
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
        }
    }

    // --- LOGICA DE REGISTRO (Submit) ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!validarEmail()) { document.getElementById('email').focus(); return; }
            if (!validarCoincidencia()) { document.getElementById('password_confirmation').focus(); return; }

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
                password_confirmation: document.getElementById('password_confirmation').value,
                id_ciudad: document.getElementById('id_ciudad').value,
                telefono: document.getElementById('telefono').value,
                celular: document.getElementById('celular').value,
                direccion: document.getElementById('direccion').value
            };

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok) {
                    localStorage.setItem('auth_token', data.token);
                    if (data.user && data.user.user_nombre) {
                        localStorage.setItem('user_name_cache', data.user.user_nombre.split(' ')[0]);
                    }
                    window.location.href = '/shop';
                } else {
                    const alerta = document.getElementById('alertError');
                    alerta.innerHTML = data.message || "Error al registrar";
                    alerta.classList.remove('d-none');
                }
            } catch (error) {
                console.error(error);
                document.getElementById('alertError').innerText = "Error de conexión";
                document.getElementById('alertError').classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        });
    }
});
