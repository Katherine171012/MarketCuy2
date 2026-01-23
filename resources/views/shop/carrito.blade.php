@extends('layouts.app')

@section('titulo', 'Mi Carrito')

@section('contenido')
    <div class="container mt-5">
        <h2 class="fw-bold mb-4">Mi Carrito</h2>

        <div class="row">
            {{-- COLUMNA IZQUIERDA: LISTA DE PRODUCTOS --}}
            <div class="col-lg-8">
                <div id="cart-items">
                    <!-- Spinner de carga inicial -->
                    <div class="text-center p-5">
                        <div class="spinner-border text-danger" role="status"></div>
                        <p class="mt-2">Cargando tus productos...</p>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: RESUMEN --}}
            <div class="col-lg-4">
                <div class="card p-4 shadow-sm border-0 bg-white sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-3">Resumen del Pedido</h4>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span id="subtotal" class="fw-bold">$0.00</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">IVA (15%)</span>
                        <span id="iva" class="fw-bold">$0.00</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-4">
                        <span>Total</span>
                        <span id="total" class="text-danger">$0.00</span>
                    </div>

                    {{-- Botón de Checkout --}}
                    <button id="btn-finalizar" onclick="irAlCheckout()" class="btn btn-danger w-100 mt-3 py-2 fw-bold"
                        disabled>
                        Finalizar Compra
                    </button>

                    <button onclick="vaciarCarrito()" class="btn btn-link btn-sm text-muted w-100 mt-2">
                        Vaciar carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Obtener Token de autenticación (Sanctum)
        const getToken = () => localStorage.getItem('auth_token');

        // 2. Cargar datos del carrito desde PostgreSQL
        async function fetchCart() {
            try {
                const response = await fetch('/api/carrito', {
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 401) {
                    window.location.href = '/login'; // Redirigir si la sesión expiró
                    return;
                }

                const data = await response.json();
                renderCart(data.items, data.subtotal);
                actualizarContadorNav(data.items);

            } catch (error) {
                console.error("Error cargando el carrito:", error);
                document.getElementById('cart-items').innerHTML =
                    '<div class="alert alert-danger">Error al conectar con el servidor.</div>';
            }
        }

        // 3. Renderizar el HTML de los productos
        function renderCart(items, subtotalGeneral) {
            const container = document.getElementById('cart-items');
            const btnFinalizar = document.getElementById('btn-finalizar');
            let html = '';

            if (!items || items.length === 0) {
                container.innerHTML = `
                            <div class="text-center p-5 bg-white rounded shadow-sm">
                                <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tu carrito está vacío.</p>
                                <a href="{{ route('productos.index') }}" class="btn btn-outline-danger btn-sm">Ir a comprar</a>
                            </div>`;
                updateTotals(0);
                btnFinalizar.disabled = true;
                return;
            }

            btnFinalizar.disabled = false;

            items.forEach(item => {
                const prod = item.producto;
                const precio = parseFloat(prod.pro_precio_venta);
                const subItem = item.cantidad * precio;
                const imgUrl = prod.pro_imagen || 'https://placehold.co/80?text=Sin+Imagen';

                html += `
                        <div class="card mb-3 p-3 shadow-sm border-0 animate__animated animate__fadeIn" data-cart-id="${item.id}" data-product-id="${prod.id_producto}">
                            <div class="row align-items-center">
                                <!-- Imagen -->
                                <div class="col-3 col-md-2">
                                    <img src="${imgUrl}" class="img-fluid rounded" style="max-height: 80px; width: 100%; object-fit: contain;">
                                </div>

                                <!-- Info -->
                                <div class="col-9 col-md-5">
                                    <h6 class="fw-bold mb-0 text-dark">${prod.pro_nombre}</h6>
                                    <small class="text-muted">$${precio.toFixed(2)} c/u</small>
                                </div>

                                <!-- Cantidad -->
                                <div class="col-6 col-md-3 text-center mt-3 mt-md-0">
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <button class="btn btn-white border btn-qty-minus" data-product="${prod.id_producto}">
                                            <i class="fa-solid fa-minus text-muted"></i>
                                        </button>
                                        <span class="px-3 py-1 bg-white border-top border-bottom d-flex align-items-center justify-content-center fw-bold qty-display" data-product="${prod.id_producto}" data-precio="${precio}" style="min-width: 45px;">
                                            ${item.cantidad}
                                        </span>
                                        <button class="btn btn-white border btn-qty-plus" data-product="${prod.id_producto}">
                                            <i class="fa-solid fa-plus text-muted"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Subtotal y Eliminar -->
                                <div class="col-6 col-md-2 text-end mt-3 mt-md-0">
                                    <div class="fw-bold text-danger fs-5 item-subtotal" data-product="${prod.id_producto}">$${subItem.toFixed(2)}</div>
                                    <button class="btn btn-link text-muted p-0 mt-1 hover-danger btn-delete" data-cart-id="${item.id}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
            });

            container.innerHTML = html;
            attachEventListeners();
            updateTotals(subtotalGeneral);
        }

        // Vincular eventos a botones después de renderizar
        function attachEventListeners() {
            document.querySelectorAll('.btn-qty-plus').forEach(btn => {
                btn.onclick = () => changeQty(btn.dataset.product, 1);
            });
            document.querySelectorAll('.btn-qty-minus').forEach(btn => {
                btn.onclick = () => changeQty(btn.dataset.product, -1);
            });
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.onclick = () => deleteItem(btn.dataset.cartId);
            });
        }

        // 4. Actualizar Totales
        function updateTotals(subtotal) {
            const IVA_RATE = 0.15;
            const iva = subtotal * IVA_RATE;
            const total = subtotal + iva;

            document.getElementById('subtotal').innerText = `$${subtotal.toFixed(2)}`;
            document.getElementById('iva').innerText = `$${iva.toFixed(2)}`;
            document.getElementById('total').innerText = `$${total.toFixed(2)}`;
        }

        // 5. Cambiar Cantidad - UI OPTIMISTA
        async function changeQty(id_producto, cantidadCambio) {
            const qtyEl = document.querySelector(`.qty-display[data-product="${id_producto}"]`);
            const subEl = document.querySelector(`.item-subtotal[data-product="${id_producto}"]`);

            if (!qtyEl) return;

            const precio = parseFloat(qtyEl.dataset.precio) || 0;
            const cantidadActual = parseInt(qtyEl.innerText.trim(), 10) || 0;
            const nuevaCantidad = cantidadActual + cantidadCambio;

            if (nuevaCantidad < 1) return;

            // ✅ Actualización INSTANTÁNEA
            qtyEl.innerText = nuevaCantidad;
            if (subEl) {
                subEl.innerText = `$${(nuevaCantidad * precio).toFixed(2)}`;
            }
            recalcularTotales();

            // Actualizar badge navbar
            const counter = document.getElementById('cartCounter');
            if (counter) {
                const currentCount = parseInt(counter.innerText || '0', 10);
                counter.innerText = currentCount + cantidadCambio;
            }

            try {
                const response = await fetch('/api/carrito/agregar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id_producto: id_producto, cantidad: cantidadCambio })
                });

                const resData = await response.json();

                if (!response.ok) {
                    // Revertir si falla
                    qtyEl.innerText = cantidadActual;
                    if (subEl) subEl.innerText = `$${(cantidadActual * precio).toFixed(2)}`;
                    recalcularTotales();
                    alert(resData.error || "No se pudo actualizar el stock.");
                    return;
                }

                // Sincronizar con servidor
                setTimeout(() => fetchCart(), 300);
            } catch (e) {
                console.error(e);
                qtyEl.innerText = cantidadActual;
                if (subEl) subEl.innerText = `$${(cantidadActual * precio).toFixed(2)}`;
                recalcularTotales();
            }
        }

        // Recalcular totales desde DOM
        function recalcularTotales() {
            let subtotal = 0;
            document.querySelectorAll('.qty-display').forEach(el => {
                const cantidad = parseInt(el.innerText.trim(), 10) || 0;
                const precio = parseFloat(el.dataset.precio) || 0;
                subtotal += cantidad * precio;
            });

            const iva = subtotal * 0.15;
            const total = subtotal + iva;
            updateTotals(subtotal);
        }

        // 6. Eliminar - ULTRA RÁPIDO
        async function deleteItem(id_carrito) {
            if (!confirm("¿Deseas quitar este producto de tu carrito?")) return;

            const itemCard = document.querySelector(`[data-cart-id="${id_carrito}"]`);
            if (!itemCard) return;

            // ✅ Animación INSTANTÁNEA
            itemCard.style.transition = 'all 0.2s';
            itemCard.style.opacity = '0';
            itemCard.style.transform = 'translateX(-20px)';

            setTimeout(() => {
                itemCard.remove();
                recalcularTotales();

                const itemsRestantes = document.querySelectorAll('[data-cart-id]').length;
                actualizarContadorNav(itemsRestantes);

                if (itemsRestantes === 0) {
                    const container = document.getElementById('cart-items');
                    container.innerHTML = `
                                <div class="text-center p-5 bg-white rounded shadow-sm">
                                    <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tu carrito está vacío.</p>
                                    <a href="{{ route('productos.index') }}" class="btn btn-outline-danger btn-sm">Ir a comprar</a>
                                </div>`;
                    updateTotals(0);
                    document.getElementById('btn-finalizar').disabled = true;
                }
            }, 200);

            try {
                const response = await fetch(`/api/carrito/eliminar/${id_carrito}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) setTimeout(() => fetchCart(), 500);
            } catch (e) {
                console.error(e);
                setTimeout(() => fetchCart(), 500);
            }
        }

        // 7. Vaciar todo
        async function vaciarCarrito() {
            if (!confirm("¿Estás seguro de que quieres vaciar todo el carrito?")) return;

            try {
                await fetch('/api/carrito/vaciar', {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                });
                fetchCart();
            } catch (e) {
                console.error(e);
            }
        }

        // 8. Utilidad: Actualizar numerito en Navbar
        function actualizarContadorNav(items) {
            let totalQty;
            if (typeof items === 'number') {
                totalQty = items;
            } else {
                totalQty = items.reduce((acc, item) => acc + item.cantidad, 0);
            }
            const counter = document.getElementById('cartCounter');
            if (counter) {
                counter.innerText = totalQty;
                counter.style.display = totalQty > 0 ? 'block' : 'none';
                localStorage.setItem('cart_count_cache', totalQty);
            }
        }

        function irAlCheckout() {
            window.location.href = "{{ route('checkout.index') }}";
        }

        // Iniciar carga al entrar
        document.addEventListener('DOMContentLoaded', fetchCart);
    </script>

    <style>
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
