@extends('layouts.app')

@section('titulo', 'Mi Carrito')

@section('contenido')
    <div class="container mt-5" x-data="carritoApp()" x-init="init()">
        <h2 class="fw-bold mb-4">Mi Carrito</h2>

        <div class="row">
            {{-- COLUMNA IZQUIERDA: LISTA DE PRODUCTOS --}}
            <div class="col-lg-8">
                <div id="cart-items">
                    {{-- Loading spinner --}}
                    <div x-show="loading" class="text-center p-5">
                        <div class="spinner-border text-danger" role="status"></div>
                        <p class="mt-2">Cargando tus productos...</p>
                    </div>

                    {{-- Empty cart --}}
                    <div x-show="!loading && items.length === 0" x-cloak class="text-center p-5 bg-white rounded shadow-sm">
                        <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tu carrito está vacío.</p>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-danger btn-sm">Ir a comprar</a>
                    </div>

                    {{-- Cart items --}}
                    <template x-for="item in items" :key="item.id">
                        <div class="card mb-3 p-3 shadow-sm border-0 animate__animated animate__fadeIn">
                            <div class="row align-items-center">
                                {{-- Imagen --}}
                                <div class="col-3 col-md-2">
                                    <img :src="item.producto.pro_imagen || 'https://placehold.co/80?text=Sin+Imagen'"
                                        class="img-fluid rounded"
                                        style="max-height: 80px; width: 100%; object-fit: contain;" loading="lazy"
                                        alt="Producto">
                                </div>

                                {{-- Info --}}
                                <div class="col-9 col-md-5">
                                    <h6 class="fw-bold mb-0 text-dark" x-text="item.producto.pro_nombre"></h6>
                                    <small class="text-muted"
                                        x-text="'$' + parseFloat(item.producto.pro_precio_venta).toFixed(2) + ' c/u'"></small>
                                </div>

                                {{-- Cantidad --}}
                                {{-- Cantidad --}}
                                <div class="col-6 col-md-3 text-center mt-3 mt-md-0">
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <button class="btn btn-white border"
                                            @click="changeQty(item.producto.id_producto, -1)">
                                            <i class="fa-solid fa-minus text-muted"></i>
                                        </button>

                                        <!-- Input manual de cantidad -->
                                        <input type="text"
                                            class="form-control text-center border-top border-bottom border-start-0 border-end-0 rounded-0 px-0 fw-bold"
                                            style="width: 50px; min-width: 50px;" :value="item.cantidad"
                                            @keydown="validarInput($event)"
                                            @change="updateManual(item, $event.target.value)"
                                            @blur="updateManual(item, $event.target.value)" @focus="$event.target.select()">

                                        <button class="btn btn-white border"
                                            @click="changeQty(item.producto.id_producto, 1)">
                                            <i class="fa-solid fa-plus text-muted"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Subtotal y Eliminar --}}
                                <div class="col-6 col-md-2 text-end mt-3 mt-md-0">
                                    <div class="fw-bold text-danger fs-5"
                                        x-text="'$' + (item.cantidad * parseFloat(item.producto.pro_precio_venta)).toFixed(2)">
                                    </div>
                                    <button class="btn btn-link text-muted p-0 mt-1 hover-danger"
                                        @click="confirmarEliminar(item.id, item.producto.pro_nombre)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- COLUMNA DERECHA: RESUMEN --}}
            <div class="col-lg-4">
                <div class="card p-4 shadow-sm border-0 bg-white sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-3">Resumen del Pedido</h4>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold" x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">IVA (15%)</span>
                        <span class="fw-bold" x-text="'$' + iva.toFixed(2)"></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-4">
                        <span>Total</span>
                        <span class="text-danger" x-text="'$' + total.toFixed(2)"></span>
                    </div>

                    {{-- Botón de Checkout --}}
                    <button @click="irAlCheckout()" class="btn btn-concho w-100 mt-3 py-2 fw-bold"
                        :disabled="items.length === 0">
                        Finalizar Compra <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>

                    <button @click="confirmarVaciar()" class="btn btn-link btn-sm text-muted w-100 mt-2"
                        :disabled="items.length === 0">
                        Vaciar carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN ELEGANTE --}}
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div class="w-100 text-center">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                            <i class="fa-solid fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center px-4">
                    <h5 class="fw-bold mb-2" id="confirmTitle">¿Estás seguro?</h5>
                    <p class="text-muted mb-4" id="confirmMessage"></p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-danger" id="confirmBtn">
                            <i class="fa-solid fa-check me-2"></i>Sí, continuar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR DE TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toastNotification" class="toast align-items-center border-0 shadow-lg" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="me-2"></i>
                    <span id="toastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        function carritoApp() {
            return {
                items: [],
                loading: true,
                pendingUpdates: {}, // ⏳ Cola de actualizaciones pendientes (Debounce)
                IVA_RATE: 0.15,

                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.cantidad * parseFloat(item.producto.pro_precio_venta)), 0);
                },

                get iva() { return this.subtotal * this.IVA_RATE; },
                get total() { return this.subtotal + this.iva; },

                async init() {
                    await this.fetchCart();
                },

                async fetchCart() {
                    try {
                        const response = await fetch('/api/carrito', {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
                            }
                        });
                        if (response.status === 401) { window.location.href = '/login'; return; }
                        const data = await response.json();

                        // Corrección de rutas de imagen al cargar
                        this.items = (data.items || []).map(item => {
                            if (item.producto.pro_imagen && !item.producto.pro_imagen.startsWith('http')) {
                                // Asegurar que use /images/ como prefijo si es ruta relativa
                                const img = item.producto.pro_imagen.replace(/^\/?(storage\/)?/, '');
                                item.producto.pro_imagen = `/images/${img}`;
                            }
                            return item;
                        });

                        this.loading = false;
                        this.actualizarContadorNav();
                    } catch (error) {
                        console.error("Error cargando carrito:", error);
                        this.loading = false;
                    }
                },

                // Validación estricta solo números (sin alertas)
                validarInput(e) {
                    // Permitir: borrado, tab, flechas, inicio, fin
                    if ([46, 8, 9, 27, 13, 110, 190, 35, 36, 37, 39].indexOf(e.keyCode) !== -1 ||
                        // Permitir: Ctrl+A, Command+A
                        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true))) {
                        return;
                    }
                    // Bloquear todo lo que no sea número
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                },

                changeQty(idProducto, cambio) {
                    const item = this.items.find(i => i.producto.id_producto === idProducto);
                    if (!item) return;

                    const nuevaCantidad = item.cantidad + cambio;
                    if (nuevaCantidad < 1) return; // Mínimo 1

                    // 1. UI INSTANTÁNEA (Optimista) 🚀
                    item.cantidad = nuevaCantidad;
                    this.actualizarContadorNav(); // Feedback inmediato en navbar

                    // 2. DEBOUNCE (Agrupar peticiones)
                    // Si ya hay un envío pendiente para este producto, cancelar el anterior
                    if (this.pendingUpdates[idProducto]) {
                        clearTimeout(this.pendingUpdates[idProducto].timeout);
                        // Sumar al delta acumulado
                        this.pendingUpdates[idProducto].delta += cambio;
                    } else {
                        // Iniciar nuevo trackeo
                        this.pendingUpdates[idProducto] = {
                            delta: cambio,
                            timeout: null,
                            originalQty: item.cantidad - cambio // Guardar valor original porsiaca falla todo el batch
                        };
                    }

                    // Configurar el envío tras 500ms de inactividad
                    this.pendingUpdates[idProducto].timeout = setTimeout(() => {
                        this.enviarActualizacion(idProducto);
                    }, 500);
                },

                updateManual(item, valorString) {
                    let nuevoValor = parseInt(valorString);
                    if (isNaN(nuevoValor) || nuevoValor < 1) {
                        nuevoValor = 1; // Mínimo seguro
                    }

                    // Calcular diferencia (delta) respecto al valor actual conocido por Alpine
                    const delta = nuevoValor - item.cantidad;

                    if (delta !== 0) {
                        this.changeQty(item.producto.id_producto, delta);
                    }

                    // Asegurar consistencia visual
                    this.$nextTick(() => {
                        // Si el valor era inválido, Alpine necesita un empujón para refrescar el :value
                        if (item.cantidad !== nuevoValor && delta === 0) {
                            // Caso borde donde escribe un valor inválido y queremos restaurar
                            // pero changeQty no corrió. Forzar actualización.
                        }
                    });
                },

                async enviarActualizacion(idProducto) {
                    const update = this.pendingUpdates[idProducto];
                    if (!update) return;

                    const delta = update.delta;
                    delete this.pendingUpdates[idProducto]; // Limpiar cola antes de enviar

                    if (delta === 0) return; // Si sumó y restó y quedó igual, no hacer nada

                    try {
                        const response = await fetch('/api/carrito/agregar', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                id_producto: idProducto,
                                cantidad: delta
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            // Si falla, revertir visualmente
                            // Nota: esto puede ser inexacto si hubo muchas interacciones, 
                            // pero es mejor que dejarlo desincronizado.
                            const item = this.items.find(i => i.producto.id_producto === idProducto);
                            if (item) {
                                // Forzar recarga del carrito para asegurar consistencia real
                                await this.fetchCart();
                            }
                            this.mostrarToast(data.error || "Error de sincronización", 'error');
                        } else {
                            // Éxito silencioso (o toast muy sutil, pero el usuario pidió "instantáneo")
                            // Opcional: console.log('Sincronizado');
                        }
                    } catch (error) {
                        console.error(error);
                        // Revertir full en error de red
                        await this.fetchCart();
                        this.mostrarToast('Error de conexión', 'error');
                    }
                },

                confirmarEliminar(idCarrito, nombreProducto) {
                    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    document.getElementById('confirmTitle').innerText = '¿Quitar producto?';
                    document.getElementById('confirmMessage').innerText = `¿Deseas eliminar "${nombreProducto}" de tu carrito?`;

                    const confirmBtn = document.getElementById('confirmBtn');
                    confirmBtn.onclick = async () => {
                        modal.hide();
                        await this.deleteItem(idCarrito);
                    };

                    modal.show();
                },

                async deleteItem(idCarrito) {
                    // ✅ Eliminación optimista
                    const itemIndex = this.items.findIndex(i => i.id === idCarrito);
                    if (itemIndex === -1) {
                        console.error('Item no encontrado:', idCarrito);
                        return;
                    }

                    const itemBackup = this.items[itemIndex];
                    this.items.splice(itemIndex, 1);

                    try {
                        const response = await fetch(`/api/carrito/eliminar/${idCarrito}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            // Si es 404, significa que ya no existe en BD. ¡Eso es bueno!
                            if (response.status === 404) {
                                // No restaurar. Considerarlo eliminado.
                                this.actualizarContadorNav();
                                // Opcional: mostrar mensaje diferente
                                // this.mostrarToast('El producto ya no estaba en el carrito', 'info');
                                return;
                            }

                            // Restaurar fallo real (ej. 500, 401)
                            console.error('Error del servidor:', data);
                            this.items.splice(itemIndex, 0, itemBackup);
                            this.mostrarToast(data.error || 'Error al eliminar', 'error');
                        } else {
                            this.actualizarContadorNav();
                            this.mostrarToast(data.message || 'Producto eliminado', 'success');
                        }
                    } catch (error) {
                        console.error('Error en deleteItem:', error);
                        this.items.splice(itemIndex, 0, itemBackup);
                        this.mostrarToast('Error de conexión', 'error');
                    }
                },

                confirmarVaciar() {
                    if (this.items.length === 0) return;

                    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    document.getElementById('confirmTitle').innerText = '¿Vaciar carrito?';
                    document.getElementById('confirmMessage').innerText = '¿Estás seguro de vaciar todo tu carrito? Esta acción no se puede deshacer.';

                    const confirmBtn = document.getElementById('confirmBtn');
                    confirmBtn.onclick = async () => {
                        modal.hide();
                        await this.vaciarCarrito();
                    };

                    modal.show();
                },

                async vaciarCarrito() {
                    try {
                        const response = await fetch('/api/carrito/vaciar', {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.items = [];
                            this.actualizarContadorNav();
                            this.mostrarToast(data.message || 'Carrito vaciado', 'success');
                        } else {
                            this.mostrarToast(data.error || 'Error al vaciar', 'error');
                        }
                    } catch (error) {
                        console.error(error);
                        this.mostrarToast('Error de conexión', 'error');
                    }
                },

                async irAlCheckout() {
                    if (this.items.length === 0) {
                        this.mostrarToast('Tu carrito está vacío', 'warning');
                        return;
                    }

                    // Validar si hay tareas pendientes de sincronización (debounce activo)
                    const pendingIds = Object.keys(this.pendingUpdates);
                    if (pendingIds.length > 0) {
                        this.mostrarToast('Por favor espera a que se actualice el carrito...', 'info');
                        // Opcional: esperar activamente
                        return;
                    }

                    // Validar stock de todos los items
                    let hayError = false;
                    for (const item of this.items) {
                        const stock = parseFloat(item.producto.pro_saldo_final || 0);
                        if (item.cantidad > stock) {
                            this.mostrarToast(`El producto "${item.producto.pro_nombre}" excede el stock disponible (${stock})`, 'danger');
                            // Resaltar visualmente el item si es posible (opcional)
                            hayError = true;
                            // Corregir localmente para ayudar al usuario
                            // item.cantidad = stock; // ¿Opcional? Mejor dejar que el usuario vealo
                        }
                    }

                    if (hayError) return;

                    // Si todo está bien, navegar
                    window.location.href = '{{ route('checkout.index') }}';
                },

                actualizarContadorNav() {
                    const contador = document.getElementById('cartCounter');
                    if (contador) {
                        const total = this.items.reduce((sum, item) => sum + item.cantidad, 0);
                        contador.innerText = total;
                        contador.style.display = total > 0 ? 'block' : 'none';
                        localStorage.setItem('cart_count_cache', total);
                    }
                },

                mostrarToast(mensaje, tipo = 'success') {
                    const toastEl = document.getElementById('toastNotification');
                    const toastBody = toastEl.querySelector('.toast-body');
                    const icon = toastBody.querySelector('i');
                    const messageSpan = document.getElementById('toastMessage');

                    // Configurar colores y icono según tipo
                    if (tipo === 'success') {
                        toastEl.className = 'toast align-items-center border-0 shadow-lg bg-success text-white';
                        icon.className = 'fa-solid fa-circle-check me-2';
                    } else {
                        toastEl.className = 'toast align-items-center border-0 shadow-lg bg-danger text-white';
                        icon.className = 'fa-solid fa-circle-xmark me-2';
                    }

                    messageSpan.innerText = mensaje;

                    const toast = new bootstrap.Toast(toastEl, {
                        autohide: true,
                        delay: 3000
                    });

                    toast.show();
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .hover-danger:hover {
            color: #dc3545 !important;
        }

        .btn-white {
            background: #fff;
            color: #6c757d;
        }

        .btn-white:hover {
            background: #f8f9fa;
        }

        /* Animaciones del modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: scale(0.8);
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Toast elegante */
        .toast {
            min-width: 300px;
            border-radius: 0.5rem;
        }

        .toast-body {
            padding: 1rem 1.25rem;
        }
    </style>
@endsection