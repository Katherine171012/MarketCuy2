/**
 * VALIDACIÓN FORMULARIO DE CONTACTO
 * Agregar al final de: resources/js/app.js
 */

(function(){
    const nombre = document.getElementById('con_nombre');
    const correo = document.getElementById('con_correo');
    const tel = document.getElementById('con_telefono');
    const tipoHidden = document.getElementById('con_tipo');
    const tipoBtns = Array.from(document.querySelectorAll('.tipo-btn'));
    const errTipo = document.getElementById('err_tipo');

    const msg = document.getElementById('con_mensaje');
    const count = document.getElementById('countMsg');
    const errMsg = document.getElementById('err_mensaje');

    const frm = document.getElementById('frmContacto');
    const btnEnviar = document.getElementById('btnEnviar');

    const placeholders = {
        productos: 'Cuéntanos qué producto buscas o sobre qué necesitas información...',
        pedidos: 'Describe tu consulta e incluye tu número de pedido si lo tienes...',
        pagos: 'Detalla tu consulta sobre métodos de pago o facturación...',
        sugerencias: 'Comparte tus ideas para mejorar nuestro servicio...'
    };

    function setCount(){
        const len = (msg.value || '').length;
        count.textContent = len + ' / 500';
    }

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

    tipoBtns.forEach(b => {
        b.addEventListener('click', () => {
            tipoBtns.forEach(x => x.classList.remove('active'));
            b.classList.add('active');
            tipoHidden.value = b.dataset.tipo || '';
            msg.placeholder = placeholders[tipoHidden.value] || 'Escribe tu mensaje aquí...';
            validarTipo();
        });
    });

    nombre.addEventListener('input', validarNombre);
    correo.addEventListener('input', validarCorreo);
    msg.addEventListener('input', () => { setCount(); validarMensaje(); });

    setCount();
    if(tipoHidden.value){
        const found = tipoBtns.find(x => x.dataset.tipo === tipoHidden.value);
        if(found) found.classList.add('active');
        msg.placeholder = placeholders[tipoHidden.value] || msg.placeholder;
    }

    frm.addEventListener('submit', (e) => {
        const ok = validarNombre() & validarCorreo() & validarTipo() & validarMensaje();
        if(!ok){
            e.preventDefault();
            frm.classList.add('shake');
            setTimeout(() => frm.classList.remove('shake'), 300);
            return;
        }

        btnEnviar.disabled = true;
        btnEnviar.textContent = 'Enviando...';
    });
})();
/**
 * ANIMACIÓN HERO - DESVANECIMIENTO AL HACER SCROLL
 */
(function(){
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        // Si bajamos más de 100px, agregamos la clase 'scrolled'
        if (currentScroll > 100) {
            document.body.classList.add('scrolled');
        } else {
            document.body.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });
})();
