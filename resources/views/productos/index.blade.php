@extends('layouts.app')

@section('titulo', 'MarketCuy')

@section('contenido')

    {{-- ERRORES --}}
    @if($errors->any())
        <div class="alert alert-danger alert-soft">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MODO DETALLE (cuando viene ?view=ID) --}}
    @if(isset($productoVer) && $productoVer)

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-concho">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver a productos
            </a>
        </div>

        @php
            $cat = $productoVer->categoria?->cat_nombre ?? 'Sin categoría';
            $desc = $productoVer->pro_descripcion ?? null;
        @endphp

        <div class="text-muted small mb-3">
            Inicio &nbsp;›&nbsp; Productos &nbsp;›&nbsp;
            <span class="fw-bold">{{ $cat }}</span>
            &nbsp;›&nbsp; {{ $productoVer->pro_nombre }}
        </div>

        <div class="detail-card p-4">
            <div class="row g-4 align-items-start">
                <div class="col-lg-6">
                    @if(!empty($productoVer->pro_imagen))
                        <img class="detail-img rounded-4"
                             src="{{ asset('storage/' . $productoVer->pro_imagen) }}"
                             alt="Imagen {{ $productoVer->pro_nombre }}">
                    @else
                        <div class="detail-img rounded-4 d-flex align-items-center justify-content-center text-muted">
                            Sin imagen
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <span class="badge-cat">{{ $cat }}</span>

                    {{-- ✅ ETIQUETA DE OFERTA (si aplica) --}}
                    @if($productoVer->tieneDescuento())
                        <span class="badge-oferta ms-2">{{ $productoVer->etiquetaPromo() }}</span>
                    @endif

                    <div class="detail-title">{{ $productoVer->pro_nombre }}</div>

                    <div class="price mb-3">
                        @if($productoVer->tieneDescuento())
                            <span class="precio-antes me-2">
                                ${{ number_format((float) $productoVer->pro_precio_antes, 2) }}
                            </span>
                        @endif

                        ${{ number_format((float) $productoVer->pro_precio_venta, 2) }}
                    </div>

                    <p class="text-muted mb-3">
                        {{ !empty($desc) ? $desc : 'Sin descripción disponible.' }}
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="pill w-100">
                                <span class="text-muted">Unidad</span><br>
                                {{ $productoVer->pro_um_compra }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pill w-100">
                                <span class="text-muted">Categoría</span><br>
                                {{ $cat }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="text-muted small fw-bold">Cantidad</div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light border rounded-circle" type="button" id="btnMinus">-</button>
                            <div class="fw-bold" style="min-width:24px; text-align:center;" id="qty">1</div>
                            <button class="btn btn-light border rounded-circle" type="button" id="btnPlus">+</button>
                        </div>
                    </div>

                    <button id="btnAddToCart"
                            type="button"
                            class="btn btn-concho product-btn py-3"
                            data-id="{{ $productoVer->id_producto }}"
                            data-nombre="{{ $productoVer->pro_nombre }}"
                            data-precio="{{ $productoVer->pro_precio_venta }}">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Agregar al Carrito
                    </button>

                    <div class="text-muted small mt-2">
                        (Demo visual. Tu módulo actual es inventario/admin.)
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                const qtyEl = document.getElementById('qty');
                const btnMinus = document.getElementById('btnMinus');
                const btnPlus = document.getElementById('btnPlus');
                let cantidadActual = 1;

                if(qtyEl && btnMinus && btnPlus) {
                    btnMinus.addEventListener('click', () => {
                        cantidadActual = Math.max(1, cantidadActual - 1);
                        qtyEl.textContent = cantidadActual;
                    });
                    btnPlus.addEventListener('click', () => {
                        cantidadActual = cantidadActual + 1;
                        qtyEl.textContent = cantidadActual;
                    });
                }

                const btnAdd = document.getElementById('btnAddToCart');

                if(btnAdd) {
                    btnAdd.addEventListener('click', () => {
                        const token = localStorage.getItem('auth_token');
                        if (!token) {
                            window.location.href = "/login";
                            return;
                        }

                        const counterEl = document.getElementById('cartCounter');
                        if(counterEl) {
                            let current = parseInt(counterEl.innerText || 0);
                            let nuevoTotal = current + cantidadActual;
                            counterEl.innerText = nuevoTotal;
                            localStorage.setItem('cart_count_cache', nuevoTotal);
                        }

                        const btnTextOriginal = btnAdd.innerHTML;
                        btnAdd.innerHTML = '<i class="fa-solid fa-check"></i> ¡Agregado!';
                        btnAdd.classList.remove('btn-concho');
                        btnAdd.classList.add('btn-success');

                        setTimeout(() => {
                            btnAdd.innerHTML = btnTextOriginal;
                            btnAdd.classList.remove('btn-success');
                            btnAdd.classList.add('btn-concho');
                        }, 2000);

                        fetch('/api/cart-add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${token}`
                            },
                            body: JSON.stringify({
                                id_producto: btnAdd.getAttribute('data-id').trim(),
                                nombre: btnAdd.getAttribute('data-nombre'),
                                precio: btnAdd.getAttribute('data-precio'),
                                cantidad: cantidadActual,
                                imagen: "{{ !empty($productoVer->pro_imagen) ? asset('storage/' . $productoVer->pro_imagen) : 'https://placehold.co/100' }}"
                            })
                        })
                            .then(response => {
                                if (!response.ok) {
                                    alert("Hubo un error guardando en el carrito. Por favor intenta de nuevo.");
                                    if(counterEl) counterEl.innerText = parseInt(counterEl.innerText) - cantidadActual;
                                }
                            })
                            .catch(() => {
                                if(counterEl) counterEl.innerText = parseInt(counterEl.innerText) - cantidadActual;
                            });
                    });
                }
            })();
        </script>

    @else

        <div class="row g-4">

            {{-- SIDEBAR --}}
            <div class="col-12 col-lg-3">
                @include('productos.buscar')
            </div>

            {{-- GRID --}}
            <div class="col-12 col-lg-9">

                {{-- ✅ SECCIÓN OFERTAS (PROMOS) --}}
                @if(isset($ofertas) && $ofertas->count() > 0)
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <h5 class="fw-bold mb-0">Ofertas del día</h5>
                            <span class="text-muted small fw-bold">Aprovecha antes que se acaben</span>
                        </div>

                        <div class="row g-3">
                            @foreach($ofertas as $o)
                                @php $catO = $o->categoria?->cat_nombre ?? 'Sin categoría'; @endphp

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="product-card h-100">
                                        @if($o->tieneDescuento())
                                            <div class="badge-oferta">{{ $o->etiquetaPromo() }}</div>
                                        @endif

                                        <div class="imgwrap">
                                            @if(!empty($o->pro_imagen))
                                                <img src="{{ asset('storage/' . $o->pro_imagen) }}"
                                                     alt="Imagen {{ $o->pro_nombre }}">
                                            @else
                                                <div class="h-100 d-flex align-items-center justify-content-center text-muted small">
                                                    Sin imagen
                                                </div>
                                            @endif
                                        </div>

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
                    </div>
                @endif

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="text-muted small fw-bold">
                        {{ $productos->total() }} productos encontrados
                    </div>

                    <div class="d-none">
                        <a class="btn btn-primary"
                           href="{{ route('productos.index', ['create' => 1]) }}">
                            + Crear nuevo producto
                        </a>
                    </div>
                </div>

                <div class="row g-4" id="gridProductos">
                    @foreach($productos as $p)
                        @php
                            $cat = $p->categoria?->cat_nombre ?? 'Sin categoría';
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4 producto-item"
                             data-precio="{{ floatval($p->pro_precio_venta) }}">
                            <div class="product-card h-100">

                                {{-- ✅ BADGE PROMO EN TARJETA (si aplica) --}}
                                @if($p->tieneDescuento())
                                    <div class="badge-oferta">{{ $p->etiquetaPromo() }}</div>
                                @endif

                                <div class="imgwrap">
                                    @if(!empty($p->pro_imagen))
                                        <img src="{{ asset('storage/' . $p->pro_imagen) }}"
                                             alt="Imagen {{ $p->pro_nombre }}">
                                    @else
                                        <div class="h-100 d-flex align-items-center justify-content-center text-muted small">
                                            Sin imagen
                                        </div>
                                    @endif
                                </div>

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

                                    <a class="btn btn-concho product-btn"
                                       href="{{ route('productos.index', ['view' => $p->id_producto]) }}">
                                        Ver detalles
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

                <div class="d-flex justify-content-center mt-4">
                    {{ $productos->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>

        <script>
            (function(){
                const range = document.getElementById('rangePrecio');
                const lbl = document.getElementById('lblPrecio');
                const items = Array.from(document.querySelectorAll('.producto-item'));

                function aplicar(){
                    const max = range ? parseFloat(range.value || '999999') : Infinity;

                    items.forEach(el => {
                        const precio = parseFloat(el.getAttribute('data-precio') || '0');
                        el.style.display = (precio <= max) ? '' : 'none';
                    });

                    if(lbl && range){
                        lbl.textContent = 'Hasta $' + parseFloat(range.value || '0').toFixed(2);
                    }
                }

                if(range) range.addEventListener('input', aplicar);
                aplicar();
            })();
        </script>

    @endif
@endsection
