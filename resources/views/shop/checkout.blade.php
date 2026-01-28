@extends('layouts.app')

@section('titulo', 'Finalizar Compra')

@section('contenido')
    <div class="container mt-5">
        <h2 class="fw-bold mb-4"><i class="fa-solid fa-credit-card me-2"></i> Finalizar Compra</h2>

        <form id="checkoutForm">
            <div class="row">

                {{-- COLUMNA IZQUIERDA: DATOS DE ENVÍO --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-concho">1. Información de Envío</h5>

                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre_completo" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono / Celular</label>
                                <input type="text" name="telefono" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección Exacta</label>
                            <input type="text" name="direccion" class="form-control"
                                placeholder="Calle principal, número, referencia..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas Adicionales (Opcional)</label>
                            <textarea name="notas" class="form-control" rows="2"
                                placeholder="Ej: Dejar en portería"></textarea>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 p-4">
                        <h5 class="fw-bold mb-3 text-concho">2. Método de Pago</h5>

                        <div class="form-check p-3 border rounded mb-2 bg-light">
                            <input class="form-check-input" type="radio" name="pago" value="tarjeta" checked>
                            <label class="form-check-label fw-bold">
                                <i class="fa-regular fa-credit-card me-2"></i> Tarjeta de Crédito / Débito
                            </label>
                        </div>

                        <div class="form-check p-3 border rounded">
                            <input class="form-check-input" type="radio" name="pago" value="transferencia">
                            <label class="form-check-label fw-bold">
                                <i class="fa-solid fa-building-columns me-2"></i> Transferencia Bancaria
                            </label>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: RESUMEN --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 p-4 sticky-top bg-white" style="top: 100px;">
                        <h5 class="fw-bold mb-3">Tu Pedido</h5>

                        {{-- Lista dinámica de items --}}
                        <div id="checkout-items" class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center py-3">
                                <div class="spinner-border text-danger"></div>
                            </div>
                        </div>

                        <hr>

                        {{-- Totales --}}
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold" id="chk-subtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">IVA (15%)</span>
                            <span class="fw-bold" id="chk-iva">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fs-4 fw-bold mt-2">
                            <span>Total</span>
                            <span class="text-danger" id="chk-total">$0.00</span>
                        </div>

                        <button type="submit" id="btn-pay" class="btn btn-success w-100 py-3 mt-3 fw-bold shadow">
                            Confirmar y Pagar
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-link w-100 text-muted mt-1 text-decoration-none">
                            Volver al carrito
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        const getToken = () => localStorage.getItem('auth_token');

        document.addEventListener("DOMContentLoaded", function () {
            loadSummary();
        });

        // 1. CARGAR RESUMEN DEL CARRITO
        async function loadSummary() {
            const token = getToken();
            if (!token) { window.location.href = '/login'; return; }

            // LEER CACHE PRIMERO
            const cachedData = localStorage.getItem('cart_items_cache');
            if (cachedData) {
                const data = JSON.parse(cachedData);
                renderSummaryHTML(data.items, data.subtotal); // Pintamos inmediato
            }

            try {
                const res = await fetch('/api/carrito', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();

                // data.items es un array de objetos con { id, id_producto, cantidad, producto: {...} }
                // Guardar cache completo
                localStorage.setItem('cart_items_cache', JSON.stringify(data));
                renderSummaryHTML(data.items || [], data.subtotal || 0);

            } catch (e) { console.error(e); }
        }

        // Mueve tu lógica de "pintar" a esta función separada
        function renderSummaryHTML(items, subtotalGeneral) {
            let html = '';
            let subtotal = 0;

            // items es un array, no un objeto
            if (!items || items.length === 0) {
                document.getElementById('checkout-items').innerHTML = '<p class="text-muted">No hay productos</p>';
                document.getElementById('chk-subtotal').innerText = '$0.00';
                document.getElementById('chk-iva').innerText = '$0.00';
                document.getElementById('chk-total').innerText = '$0.00';
                return;
            }

            items.forEach(item => {
                const prod = item.producto; // relación cargada por el controlador
                if (!prod) return;

                const precio = parseFloat(prod.pro_precio_venta);
                const subItem = item.cantidad * precio;
                subtotal += subItem;

                const imgUrl = prod.pro_imagen
                    ? `/images/${prod.pro_imagen}`
                    : 'https://placehold.co/50';

                html += `
                    <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                        <img src="${imgUrl}" class="rounded me-2" width="50" height="50" style="object-fit: contain;">
                        <div class="flex-grow-1 lh-1">
                            <small class="fw-bold d-block text-truncate" style="max-width: 150px;">${prod.pro_nombre}</small>
                            <small class="text-muted">x${item.cantidad}</small>
                        </div>
                        <span class="fw-bold small">$${subItem.toFixed(2)}</span>
                    </div>
                `;
            });

            const iva = subtotal * 0.15;
            const total = subtotal + iva;

            document.getElementById('checkout-items').innerHTML = html;
            document.getElementById('chk-subtotal').innerText = `$${subtotal.toFixed(2)}`;
            document.getElementById('chk-iva').innerText = `$${iva.toFixed(2)}`;
            document.getElementById('chk-total').innerText = `$${total.toFixed(2)}`;
        }

        // 2. PROCESAR PAGO
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('btn-pay');
            const originalText = btn.innerHTML;

            // Bloquear botón
            btn.disabled = true;
            btn.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Procesando...';

            try {
                const formData = new FormData(e.target);
                const token = getToken();

                const res = await fetch('/api/checkout-process', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                        // No poner Content-Type cuando es FormData, el navegador lo pone solo
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok) {
                    // ÉXITO: Redirección inmediata a la página de confirmación
                    // Limpiamos el contador del carrito visualmente
                    const counter = document.getElementById('cartCounter');
                    if (counter) counter.innerText = '0';

                    // REDIRECCIÓN
                    window.location.href = `/confirmacion/${data.order_id}`;
                } else {
                    // ERROR (Mantenemos alerta solo para errores)
                    alert("Error: " + (data.error || "No se pudo procesar."));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }

            } catch (error) {
                console.error(error);
                alert("Error de conexión");
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
@endsection