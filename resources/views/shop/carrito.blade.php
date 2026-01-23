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
                                            @click="changeQty(item.producto.id_producto, -1)">
                                            <i class="fa-solid fa-minus text-muted"></i>
                                        </button>
                                        <span
                                            class="px-3 py-1 bg-white border-top border-bottom d-flex align-items-center justify-content-center fw-bold"
                                            style="min-width: 45px;" x-text="item.cantidad"></span>
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
                                        @click="deleteItem(item.id)">
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

                    <button @click="vaciarCarrito()" class="btn btn-link btn-sm text-muted w-100 mt-2"
                        :disabled="items.length === 0">
                        Vaciar carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function carritoApp() {
            return {
                items: [],
                loading: true,
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
                    const item = this.items.find(i => i.producto.id_producto === idProducto);
                    if (!item) return;

                    const nuevaCantidad = item.cantidad + cambio;
                    if (nuevaCantidad < 1) return;

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

                        if (!response.ok) {
                            // Revertir si falla
                            item.cantidad = cantidadOriginal;
                            const data = await response.json();
                            alert(data.error || "Error al actualizar");
                        } else {
                            this.actualizarContadorNav();
                        }
                    } catch (error) {
                        console.error(error);
                        item.cantidad = cantidadOriginal; // Revertir
                    }
                },

                async deleteItem(idCarrito) {
                    if (!confirm("¿Deseas quitar este producto de tu carrito?")) return;

                    // ✅ Eliminación optimista
                    const itemIndex = this.items.findIndex(i => i.id === idCarrito);
                    if (itemIndex === -1) return;

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

                        if (!response.ok) {
                            // Restaurar si falla
                            this.items.splice(itemIndex, 0, itemBackup);
                        } else {
                            this.actualizarContadorNav();
                        }
                    } catch (error) {
                        console.error(error);
                        this.items.splice(itemIndex, 0, itemBackup);
                    }
                },

                async vaciarCarrito() {
                    if (!confirm("¿Estás seguro de que quieres vaciar todo el carrito?")) return;

                    try {
                        await fetch('/api/carrito/vaciar', {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
                            }
                        });

                        this.items = [];
                        this.actualizarContadorNav();
                    } catch (error) {
                        console.error(error);
                    }
                },

                irAlCheckout() {
                    window.location.href = {{ route('checkout.index') }};
                },

                actualizarContadorNav() {
                    const contador = document.getElementById('cartCounter');
                    if (contador) {
                        const total = this.items.reduce((sum, item) => sum + item.cantidad, 0);
                        contador.innerText = total;
                        contador.style.display = total > 0 ? 'block' : 'none';
                        localStorage.setItem('cart_count_cache', total);
                    }
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
    </style>
@endsection
