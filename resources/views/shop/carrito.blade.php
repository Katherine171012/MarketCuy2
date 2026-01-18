@extends('layouts.app')

@section('titulo', 'Mi Carrito')

@section('contenido')
    <div class="container mt-5">
        <h2 class="fw-bold mb-4">Mi Carrito</h2>

        <div class="row">

            {{-- COLUMNA IZQUIERDA (8): LISTA DE PRODUCTOS --}}
            <div class="col-lg-8">
                <div id="cart-items">
                    <div class="text-center p-5">
                        <div class="spinner-border text-danger"></div>
                        <p>Cargando productos...</p>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA (4): RESUMEN (SIN ENVÍO) --}}
            <div class="col-lg-4">
                <div class="card p-4 shadow-sm border-0 bg-white sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-3">Resumen del Pedido</h4>

                    {{-- Subtotal --}}
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span id="subtotal" class="fw-bold">$0.00</span>
                    </div>

                    {{-- IVA (15%) --}}
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">IVA (15%)</span>
                        <span id="iva" class="fw-bold">$0.00</span>
                    </div>

                    {{-- YA NO HAY ENVÍO AQUÍ --}}

                    <hr>

                    {{-- Total --}}
                    <div class="d-flex justify-content-between fw-bold fs-4">
                        <span>Total</span>
                        <span id="total" class="text-danger">$0.00</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-danger w-100 mt-3 py-2 fw-bold">
                        Finalizar Compra
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT DE CÁLCULO (SIN ENVÍO) --}}
    <script>
        const getToken = () => localStorage.getItem('auth_token');

        // 1. Cargar datos
        async function fetchCart() {
            // Intentamos cargar lo que hay en cache primero
            const cachedItems = localStorage.getItem('cart_items_cache');
            if (cachedItems) {
                renderCart(JSON.parse(cachedItems)); // ¡PUM! Aparecen los productos al instante
            }

            try {
                const response = await fetch('/api/cart/data', {
                    headers: { 'Authorization': `Bearer ${getToken()}` }
                });
                const cart = await response.json();

                // Guardamos en cache para la próxima vez
                localStorage.setItem('cart_items_cache', JSON.stringify(cart.items || {}));

                // Actualizamos con datos reales del servidor (por si algo cambió)
                renderCart(cart.items || {});

                // Actualizamos también el numerito del navbar
                let totalQty = 0;
                Object.values(cart.items || {}).forEach(i => totalQty += i.cantidad);
                localStorage.setItem('cart_count_cache', totalQty);
                if(document.getElementById('cartCounter')) document.getElementById('cartCounter').innerText = totalQty;

            } catch (error) {
                console.error("Error cargando carrito:", error);
            }
        }

        // 2. Pintar HTML
        function renderCart(items) {
            const container = document.getElementById('cart-items');
            let html = '';
            let subtotalGeneral = 0;

            const itemsList = Object.values(items);

            if (itemsList.length === 0) {
                container.innerHTML = '<div class="alert alert-warning text-center">Tu carrito está vacío.</div>';
                updateTotals(0);
                return;
            }

            itemsList.forEach(item => {
                const subItem = parseFloat(item.subtotal);
                subtotalGeneral += subItem;

                const imgUrl = item.imagen || 'https://placehold.co/80';

                html += `
            <div class="card mb-3 p-3 shadow-sm border-0">
                <div class="row align-items-center">
                    <div class="col-3 col-md-2">
                        <img src="${imgUrl}" class="img-fluid rounded" style="max-height: 80px; object-fit: contain;">
                    </div>
                    <div class="col-9 col-md-5">
                        <h6 class="fw-bold mb-0">${item.nombre}</h6>
                        <small class="text-muted">$${parseFloat(item.precio).toFixed(2)} c/u</small>
                    </div>
                    <div class="col-6 col-md-3 text-center mt-3 mt-md-0">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="changeQty('${item.id_producto}', ${item.cantidad - 1})">-</button>
                            <span class="px-3 py-1 bg-light border d-flex align-items-center justify-content-center" style="min-width: 40px;">${item.cantidad}</span>
                            <button class="btn btn-outline-secondary" onclick="changeQty('${item.id_producto}', ${item.cantidad + 1})">+</button>
                        </div>
                    </div>
                    <div class="col-6 col-md-2 text-end mt-3 mt-md-0">
                        <div class="fw-bold text-danger">$${subItem.toFixed(2)}</div>
                        <button onclick="deleteItem('${item.id_producto}')" class="btn btn-link text-muted p-0 mt-1"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>`;
            });

            container.innerHTML = html;
            updateTotals(subtotalGeneral);
        }

        // 3. MATEMÁTICA CORREGIDA (SOLO SUBTOTAL + IVA)
        function updateTotals(subtotal) {
            const IVA_PORCENTAJE = 0.15; // 15%

            // Calculamos solo IVA y Total simple
            let iva = 0;
            let total = 0;

            if (subtotal > 0) {
                iva = subtotal * IVA_PORCENTAJE;
                total = subtotal + iva; // YA NO SUMAMOS ENVÍO
            }

            document.getElementById('subtotal').innerText = `$${subtotal.toFixed(2)}`;
            document.getElementById('iva').innerText      = `$${iva.toFixed(2)}`;
            document.getElementById('total').innerText    = `$${total.toFixed(2)}`;
        }

        // 4. Funciones de botones
        async function changeQty(id, newQty) {
            if (newQty < 1) return deleteItem(id);

            await fetch('/api/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${getToken()}`
                },
                body: JSON.stringify({ id_producto: id, cantidad: newQty })
            });
            fetchCart();
        }

        async function deleteItem(id) {
            if(!confirm("¿Quitar producto?")) return;
            await fetch(`/api/cart/remove/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            fetchCart();
        }

        document.addEventListener('DOMContentLoaded', fetchCart);
    </script>
@endsection
