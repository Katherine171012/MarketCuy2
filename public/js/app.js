/**
 * ========================================================================
 * ANIMACIÓN HERO - DESVANECIMIENTO AL HACER SCROLL (OPTIMIZADO)
 * ========================================================================
 */
(function(){
    let lastScroll = 0;
    let ticking = false;

    window.addEventListener('scroll', function() {
        lastScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (!ticking) {
            window.requestAnimationFrame(function() {
                // Threshold de 50px para activar la animación
                if (lastScroll > 50) {
                    document.body.classList.add('scrolled');
                } else {
                    document.body.classList.remove('scrolled');
                }
                ticking = false;
            });
            ticking = true;
        }
    });
})();

/**
 * ========================================================================
 * VALIDACIÓN FORMULARIO DE CONTACTO
 * ========================================================================
 */
document.addEventListener('DOMContentLoaded', function() {
    const frm = document.getElementById('frmContacto');

    // Si no existe el formulario, salir
    if (!frm) return;

    // Elementos del formulario
    const nombre = document.getElementById('con_nombre');
    const correo = document.getElementById('con_correo');
    const tel = document.getElementById('con_telefono');
    const tipoHidden = document.getElementById('con_tipo');
    const tipoBtns = Array.from(document.querySelectorAll('.tipo-btn'));
    const errTipo = document.getElementById('err_tipo');
    const msg = document.getElementById('con_mensaje');
    const count = document.getElementById('countMsg');
    const errMsg = document.getElementById('err_mensaje');
    const btnEnviar = document.getElementById('btnEnviar');

    // Placeholders dinámicos según tipo de consulta
    const placeholders = {
        productos: 'Cuéntanos qué producto buscas o sobre qué necesitas información...',
        pedidos: 'Describe tu consulta e incluye tu número de pedido si lo tienes...',
        pagos: 'Detalla tu consulta sobre métodos de pago o facturación...',
        sugerencias: 'Comparte tus ideas para mejorar nuestro servicio...'
    };

    /**
     * CONTADOR DE CARACTERES
     */
    function setCount(){
        const len = (msg.value || '').length;
        count.textContent = len + ' / 500';
    }

    /**
     * VALIDACIONES INDIVIDUALES
     */
    function validarNombre(){
        const ok = (nombre.value || '').trim().length >= 3;
        nombre.classList.toggle('is-invalid', !ok);
        nombre.classList.toggle('is-valid', ok);
        return ok;
    }

    function validarCorreo(){
        const v = (correo.value || '').trim();
        const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        correo.classList.toggle('is-invalid', !ok);
        correo.classList.toggle('is-valid', ok);
        return ok;
    }

    function validarTipo(){
        const ok = !!(tipoHidden.value || '').trim();
        errTipo.classList.toggle('d-none', ok);
        return ok;
    }

    function validarMensaje(){
        const v = (msg.value || '').trim();
        const ok = v.length >= 10 && v.length <= 500;
        errMsg.classList.toggle('d-none', ok);
        msg.classList.toggle('is-invalid', !ok);
        msg.classList.toggle('is-valid', ok);
        return ok;
    }

    /**
     * BOTONES TIPO CONSULTA
     */
    tipoBtns.forEach(b => {
        b.addEventListener('click', () => {
            // Desactivar todos los botones
            tipoBtns.forEach(x => x.classList.remove('active'));

            // Activar el botón clickeado
            b.classList.add('active');

            // Guardar tipo en campo oculto
            tipoHidden.value = b.dataset.tipo || '';

            // Cambiar placeholder dinámicamente
            msg.placeholder = placeholders[tipoHidden.value] || 'Escribe tu mensaje aquí...';

            // Validar inmediatamente
            validarTipo();
        });
    });

    /**
     * EVENT LISTENERS
     */
    nombre.addEventListener('input', validarNombre);
    nombre.addEventListener('blur', validarNombre);

    correo.addEventListener('input', validarCorreo);
    correo.addEventListener('blur', validarCorreo);

    msg.addEventListener('input', () => {
        setCount();
        validarMensaje();
    });
    msg.addEventListener('blur', validarMensaje);

    /**
     * INICIALIZACIÓN
     */
    setCount();

    // ✅ FIX: Mantener selección después de errores de validación
    if(tipoHidden.value){
        const found = tipoBtns.find(x => x.dataset.tipo === tipoHidden.value);
        if(found) {
            found.classList.add('active');
            msg.placeholder = placeholders[tipoHidden.value] || msg.placeholder;
        }
    }

    /**
     * SUBMIT DEL FORMULARIO
     */
    frm.addEventListener('submit', (e) => {
        // Validar todos los campos (usar & en vez de && para ejecutar todas las validaciones)
        const nombreOk = validarNombre();
        const correoOk = validarCorreo();
        const tipoOk = validarTipo();
        const mensajeOk = validarMensaje();

        const todosOk = nombreOk && correoOk && tipoOk && mensajeOk;

        if(!todosOk){
            e.preventDefault();

            // Animación de shake
            frm.classList.add('shake');
            setTimeout(() => frm.classList.remove('shake'), 300);

            // Scroll al primer error
            const primerError = frm.querySelector('.is-invalid');
            if(primerError){
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                primerError.focus();
            }

            return;
        }

        // Deshabilitar botón para evitar doble envío
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Enviando...';
    });
});
