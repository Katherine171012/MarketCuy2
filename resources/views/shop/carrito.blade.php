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
                                <div class="col-6 col-md-3 text-center mt-3 mt-md-0">
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <button class="btn btn-white border"
                                            @click="changeQty(item.producto.id_producto, -1)"
                                            :disabled="updatingItems[item.producto.id_producto] || false">
                                            <i class="fa-solid fa-minus text-muted"></i>
                                        </button>
                                        <span
                                            class="px-3 py-1 bg-white border-top border-bottom d-flex align-items-center justify-content-center fw-bold position-relative"
                                            style="min-width: 45px;">
                                            <span x-show="!updatingItems[item.producto.id_producto]"
                                                x-text="item.cantidad"></span>
                                            <i x-show="updatingItems[item.producto.id_producto]"
                                                class="fa-solid fa-spinner fa-spin text-danger"></i>
                                        </span>
                                        <button class="btn btn-white border"
                                            @click="changeQty(item.producto.id_producto, 1)"
                                            :disabled="updatingItems[item.producto.id_producto] || false">
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
                    <button @click="irAlCheckout()" class="btn btn-danger w-100 mt-3 py-2 fw-bold"
                        :disabled="items.length === 0">
                        Finalizar Compra
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
                updatingItems: {}, // 🔒 Track de items en proceso
                IVA_RATE: 0.15,

                // ✅ Computed properties (recalcula automáticamente)
                get subtotal() {
                    return this.items.reduce((sum, item) => {
                        return sum + (item.cantidad * parseFloat(item.producto.pro_precio_venta));
                    }, 0);
                },

                get iva() {
                    return this.subtotal * this.IVA_RATE;
                },

                get total() {
                    return this.subtotal + this.iva;
                },

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

                        if (response.status === 401) {
                            window.location.href = '/login';
                            return;
                        }

                        const data = await response.json();
                        this.items = data.items || [];
                        this.loading = false;
                        this.actualizarContadorNav();
                    } catch (error) {
                        console.error("Error cargando el carrito:", error);
                        this.loading = false;
                    }
                },

                async changeQty(idProducto, cambio) {
                    // 🔒 Prevenir clics múltiples
                    if (this.updatingItems[idProducto]) {
                        return; // Ya hay una actualización en proceso
                    }

                    const item = this.items.find(i => i.producto.id_producto === idProducto);
                    if (!item) return;

                    const nuevaCantidad = item.cantidad + cambio;
                    if (nuevaCantidad < 1) return;

                    // Marcar como en proceso
                    this.updatingItems[idProducto] = true;

                    // ✅ Actualización optimista (Alpine reactivity)
                    const cantidadOriginal = item.cantidad;
                    item.cantidad = nuevaCantidad;

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
                                cantidad: cambio
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            // Revertir si falla
                            item.cantidad = cantidadOriginal;
                            this.mostrarToast(data.error || "Error al actualizar", 'error');
                        } else {
                            this.actualizarContadorNav();
                            // Solo mostrar toast cada 3 clicks para no saturar
                            if (Math.abs(cambio) === 1 && Math.random() > 0.7) {
                                this.mostrarToast(data.message || 'Actualizado', 'success');
                            }
                        }
                    } catch (error) {
                        console.error(error);
                        item.cantidad = cantidadOriginal; // Revertir
                        this.mostrarToast('Error de conexión', 'error');
                    } finally {
                        // Liberar el lock después de un pequeño delay
                        setTimeout(() => {
                            delete this.updatingItems[idProducto];
                        }, 300);
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
                            // Restaurar si falla
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

                irAlCheckout() {
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