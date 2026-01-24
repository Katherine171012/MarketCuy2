@extends('layouts.app')

@section('titulo', 'MarketCuy')

@section('contenido')

    @if($errors->any())
        <div class="alert alert-danger alert-soft">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($productoVer) && $productoVer)

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-concho">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver a productos
            </a>
        </div>

        @php
            $cat = $productoVer->categoria?->cat_nombre ?? 'Sin categoría';
            $desc = $productoVer->pro_descripcion ?? null;
            $enStock = ((int)($productoVer->pro_saldo_final ?? 0)) > 0;
        @endphp

        <div class="detail-card p-4">
            <div class="row g-4 align-items-start">
                <div class="col-lg-6">
                    @if(!empty($productoVer->pro_imagen))
                        <img class="detail-img rounded-4"
                             src="{{ asset('images/' . $productoVer->pro_imagen) }}"
                             alt="Imagen {{ $productoVer->pro_nombre }}">
                    @else
                        <div class="detail-img rounded-4 d-flex align-items-center justify-content-center text-muted">
                            Sin imagen
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <span class="badge-cat">{{ $cat }}</span>

                    @if($productoVer->tieneDescuento())
                        <span class="badge-oferta ms-2">{{ $productoVer->etiquetaPromo() }}</span>
                    @endif

                    @if(!$enStock)
                        <span class="badge-agotado ms-2">Agotado</span>
                    @endif

                    <div class="detail-title">{{ $productoVer->pro_nombre }}</div>

                    @if($desc)
                        <div class="mt-3">
                            <div class="text-muted small fw-bold mb-1">Descripción</div>
                            <div class="text-muted" style="line-height:1.65;">
                                {{ $desc }}
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                        <div>
                            @if($productoVer->tieneDescuento())
                                <div class="precio-antes">
                                    ${{ number_format((float) $productoVer->pro_precio_antes, 2) }}
                                </div>
                            @endif

                            <div class="display-6 fw-bold mb-0">
                                ${{ number_format((float) $productoVer->pro_precio_venta, 2) }}
                            </div>

                            <div class="small text-muted mt-1">
                                Stock: <span class="fw-bold">{{ (int)($productoVer->pro_saldo_final ?? 0) }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-secondary" id="btnMinus" {{ !$enStock ? 'disabled' : '' }}>-</button>
                            <input id="txtQty" type="text" class="form-control text-center" value="1" style="width:60px;" {{ !$enStock ? 'disabled' : '' }}>
                            <button class="btn btn-outline-secondary" id="btnPlus" {{ !$enStock ? 'disabled' : '' }}>+</button>
                        </div>
                    </div>

                    <div class="row g-2 mt-3">
                        <div class="col-12">
                            <div class="pill">
                                <div class="text-muted small fw-bold">Unidad</div>
                                <div class="fw-bold">
                                    {{ $productoVer->unidadVenta?->um_descripcion ?? $productoVer->unidadCompra?->um_descripcion ?? 'N/D' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <button class="btn btn-concho btn-lg"
                                id="btnAddCarrito"
                                data-id="{{ $productoVer->id_producto }}"
                                data-nombre="{{ $productoVer->pro_nombre }}"
                                data-precio="{{ $productoVer->pro_precio_venta }}"
                            {{ !$enStock ? 'disabled' : '' }}>
                            <i class="fa-solid fa-cart-plus me-2"></i>
                            {{ !$enStock ? 'No disponible' : 'Agregar al carrito' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                const enStock = {{ $enStock ? 'true' : 'false' }};

                const minus = document.getElementById('btnMinus');
                const plus  = document.getElementById('btnPlus');
                const qty   = document.getElementById('txtQty');

                function clamp(){
                    let n = parseInt(qty.value || '1', 10);
                    if(isNaN(n) || n < 1) n = 1;
                    qty.value = n;
                    return n;
                }

                if(enStock){
                    if(minus) minus.addEventListener('click', () => { clamp(); qty.value = Math.max(1, parseInt(qty.value,10)-1); });
                    if(plus)  plus.addEventListener('click',  () => { clamp(); qty.value = parseInt(qty.value,10)+1; });
                    
                    if(qty) {
                        // Validación estricta solo números ENTEROS (sin puntos)
                        qty.addEventListener('keydown', (e) => {
                             // Permitir: borrado, tab, flechas, inicio, fin, ctrl+a
                             // Se eliminaron 110 y 190 (puntos decimales)
                            if ([46, 8, 9, 27, 13, 35, 36, 37, 39].indexOf(e.keyCode) !== -1 ||
                                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true))) {
                                return;
                            }
                            // Permitir números del teclado principal y numpad
                            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                                e.preventDefault();
                            }
                        });

                        qty.addEventListener('blur', clamp);
                        qty.addEventListener('change', clamp);
                    }
                }

                const btnAdd = document.getElementById('btnAddCarrito');
                if(btnAdd){
                    btnAdd.addEventListener('click', async () => {
                        if(!enStock) return;

                        const stockMax = parseInt("{{ (int)($productoVer->pro_saldo_final ?? 0) }}");
                        const cantidadActual = clamp();
                        
                        // Validación bonita de stock excedido
                        if(cantidadActual > stockMax) {
                            const msg = `Solo quedan ${stockMax} unidades disponibles`;
                            
                            // Función helper para toast si no existe
                            const showToast = (message, type = 'danger') => {
                                let container = document.getElementById('toast-container-global');
                                if (!container) {
                                    container = document.createElement('div');
                                    container.id = 'toast-container-global';
                                    container.className = 'toast-container position-fixed top-0 end-0 p-3';
                                    container.style.zIndex = '9999';
                                    document.body.appendChild(container);
                                }

                                const toastHtml = `
                                    <div class="toast show align-items-center text-white bg-${type} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="d-flex">
                                            <div class="toast-body fs-6 fw-semibold">
                                                <i class="fa-solid fa-circle-exclamation me-2"></i> ${message}
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </div>
                                `;
                                
                                const toastEl = document.createElement('div');
                                toastEl.innerHTML = toastHtml;
                                const toastNode = toastEl.firstElementChild;
                                container.appendChild(toastNode);
                                
                                setTimeout(() => {
                                    toastNode.classList.remove('show');
                                    setTimeout(() => toastNode.remove(), 300);
                                }, 3000);
                            };

                            showToast(msg, 'danger');
                            qty.value = stockMax; // Ajustar al máximo
                            return;
                        }

                        const token = localStorage.getItem('auth_token');

                        if(!token){
                            window.location.href = "/login";
                            return;
                        }

                        // Deshabilitar botón temporalmente
                        const btnTextOriginal = btnAdd.innerHTML;
                        btnAdd.disabled = true;
                        btnAdd.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Agregando...';

                        try {
                            const response = await fetch('/api/carrito/agregar', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': `Bearer ${token}`,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    id_producto: btnAdd.getAttribute('data-id').trim(),
                                    cantidad: cantidadActual
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                // Actualizar contador del navbar
                                const counterEl = document.getElementById('cartCounter');
                                if(counterEl) {
                                    let current = parseInt(counterEl.innerText || 0);
                                    let nuevoTotal = current + cantidadActual;
                                    counterEl.innerText = nuevoTotal;
                                    counterEl.style.display = nuevoTotal > 0 ? 'block' : 'none';
                                    localStorage.setItem('cart_count_cache', nuevoTotal);
                                }

                                // Mostrar éxito
                                btnAdd.innerHTML = '<i class="fa-solid fa-check"></i> ¡Agregado!';
                                btnAdd.classList.remove('btn-concho');
                                btnAdd.classList.add('btn-success');

                                setTimeout(() => {
                                    btnAdd.innerHTML = btnTextOriginal;
                                    btnAdd.classList.remove('btn-success');
                                    btnAdd.classList.add('btn-concho');
                                    btnAdd.disabled = false;
                                }, 2000);
                            } else {
                                // Mostrar error del servidor
                                alert(data.error || "No se pudo agregar el producto al carrito");
                                btnAdd.innerHTML = btnTextOriginal;
                                btnAdd.disabled = false;
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert("Error de conexión. Por favor intenta de nuevo.");
                            btnAdd.innerHTML = btnTextOriginal;
                            btnAdd.disabled = false;
                        }
                    });
                }
            })();
        </script>

    @else

        <div class="row g-4">

            <div class="col-12 col-lg-3">
                @include('productos.buscar')
            </div>

            <div class="col-12 col-lg-9">

                <div id="productosContenido">

                    @php
                        $catFiltro = request('categoria', request('id_categoria'));
                        $qFiltro   = request('q');
                        $umFiltro  = request('unidad_medida');
                        $ordFiltro = request('orden');

                        $catVal = strtolower(trim((string)($catFiltro ?? '')));
                        $catActivo = ($catVal !== '' && !in_array($catVal, ['0','all','todas','toda','*'], true));

                        $ordVal = strtolower(trim((string)($ordFiltro ?? '')));
                        $ordenActivo = ($ordVal !== '' && !in_array($ordVal, ['mix','id_asc','default'], true));

                        $umVal = strtolower(trim((string)($umFiltro ?? '')));
                        $unidadActiva = ($umVal !== '' && !in_array($umVal, ['all','todas','toda','*'], true));

                        $hayFiltros = $catActivo || $ordenActivo || $unidadActiva || ($qFiltro !== null && trim($qFiltro) !== '');

                        $enPrimeraPagina = isset($productos) && method_exists($productos, 'currentPage') ? ((int)$productos->currentPage() === 1) : ((int)request('page', 1) === 1);
                    @endphp

                    @if(!$hayFiltros && $enPrimeraPagina && isset($ofertas) && $ofertas && $ofertas->count() > 0)
                        <div id="ofertasSection" class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="mb-0 fw-bold">Ofertas destacadas</h5>
                            </div>

                            <div class="row g-3">
                                @foreach($ofertas as $o)
                                    @php
                                        $img = !empty($o->pro_imagen) ? asset('images/' . $o->pro_imagen) : 'https://placehold.co/600x450';
                                        $catO = $o->categoria?->cat_nombre ?? 'Sin categoría';
                                    @endphp
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="product-card h-100 position-relative">
                                            <div class="imgwrap">
                                                <img src="{{ $img }}" alt="Imagen {{ $o->pro_nombre }}">
                                            </div>

                                            <span class="badge-oferta">{{ $o->etiquetaPromo() }}</span>

                                            <div class="p-3">
                                                <span class="badge-cat">{{ $catO }}</span>

                                                <div class="product-title">{{ $o->pro_nombre }}</div>

                                                <div class="price mb-3">
                                                    <span class="precio-antes me-2">
                                                        ${{ number_format((float) $o->pro_precio_antes, 2) }}
                                                    </span>
                                                    ${{ number_format((float) $o->pro_precio_venta, 2) }}
                                                </div>

                                                <a class="btn btn-concho product-btn"
                                                   href="{{ route('productos.index', ['view' => $o->id_producto]) }}">
                                                    Ver detalles
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-4">
                        </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0 fw-bold">
                            @if($hayFiltros) Resultados @else Productos @endif
                        </h5>
                        <div class="text-muted small fw-bold" id="lblTotalProductos">
                            {{ $productos->total() }} producto(s)
                        </div>
                    </div>

                    <div class="row g-3" id="gridProductos">
                        @foreach($productos as $p)
                            @php
                                $img = !empty($p->pro_imagen) ? asset('images/' . $p->pro_imagen) : 'https://placehold.co/600x450';
                                $cat = $p->categoria?->cat_nombre ?? 'Sin categoría';
                                $enStock = ((int)($p->pro_saldo_final ?? 0)) > 0;
                            @endphp

                            <div class="col-12 col-md-6 col-lg-4 producto-item"
                                 data-precio="{{ (float)$p->pro_precio_venta }}">
                                <div class="product-card h-100 position-relative">
                                    <div class="imgwrap">
                                        <img src="{{ $img }}" alt="Imagen {{ $p->pro_nombre }}">
                                    </div>

                                    @if($p->tieneDescuento())
                                        <span class="badge-oferta">{{ $p->etiquetaPromo() }}</span>
                                    @endif

                                    @if(!$enStock)
                                        <span class="badge-agotado">Agotado</span>
                                    @endif

                                    <div class="p-3">
                                        <span class="badge-cat">{{ $cat }}</span>

                                        <div class="product-title">{{ $p->pro_nombre }}</div>

                                        <div class="price mb-3">
                                            @if($p->tieneDescuento())
                                                <span class="precio-antes me-2">
                                                    ${{ number_format((float) $p->pro_precio_antes, 2) }}
                                                </span>
                                            @endif
                                            ${{ number_format((float) $p->pro_precio_venta, 2) }}
                                        </div>

                                        <a class="btn btn-concho product-btn {{ !$enStock ? 'disabled' : '' }}"
                                           href="{{ $enStock ? route('productos.index', ['view' => $p->id_producto]) : 'javascript:void(0)' }}"
                                            {{ !$enStock ? 'aria-disabled=true tabindex=-1' : '' }}>
                                            {{ !$enStock ? 'No disponible' : 'Ver detalles' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($productos->count() === 0)
                            <div class="col-12">
                                <div class="alert alert-warning alert-soft mb-0">
                                    {{ $info ?? 'Sin registros' }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-center mt-4" id="paginacionProductos">
                        {{ $productos->links('pagination::bootstrap-4') }}
                    </div>

                </div>

            </div>
        </div>
        /* ====== funcion ====== */
        <script>
            (function(){
                const range = document.getElementById('rangePrecio');
                const lbl = document.getElementById('lblPrecio');

                const maxDefault = range
                    ? parseFloat(range.max || range.getAttribute('max') || range.value || '0')
                    : null;

                function aplicar(){
                    const max = range ? parseFloat(range.value || '999999') : Infinity;

                    const items = Array.from(document.querySelectorAll('.producto-item'));
                    items.forEach(el => {
                        const precio = parseFloat(el.getAttribute('data-precio') || '0');
                        el.style.display = (precio <= max) ? '' : 'none';
                    });

                    if(lbl && range){
                        lbl.textContent = 'Hasta $' + parseFloat(range.value || '0').toFixed(2);
                    }

                    const ofertasSection = document.getElementById('ofertasSection');
                    if(ofertasSection && range && maxDefault !== null){
                        const sliderActual = parseFloat(range.value || '0');
                        const mostrarOfertas = (sliderActual >= maxDefault);
                        ofertasSection.style.display = mostrarOfertas ? '' : 'none';
                    }
                }

                window.applyPriceFilter = aplicar;

                if(range) range.addEventListener('input', aplicar);
                aplicar();
            })();
        </script>
    @endif
@endsection
