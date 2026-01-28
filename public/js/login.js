/* public/js/login.js */
document.addEventListener('DOMContentLoaded', function() {
    // --- LÓGICA DEL OJITO ---
    const btnToggle = document.getElementById('btnToggle');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }

    // --- LÓGICA DE LOGIN ---
    const form = document.getElementById('loginForm');
    const alerta = document.getElementById('alertError');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
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
                        'Accept': 'application/json',
                        // Laravel CSRF Token (opcional si es API pura, pero recomendado)
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('auth_token', data.token);
                    if (data.user && data.user.user_nombre) {
                        const firstName = data.user.user_nombre.split(' ')[0];
                        localStorage.setItem('user_name_cache', firstName);
                    } else {
                        localStorage.setItem('user_name_cache', 'Usuario');
                    }
                    window.location.href = '/shop';
                } else {
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
    }
});
