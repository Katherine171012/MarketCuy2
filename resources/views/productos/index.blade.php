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

                    <div class="detail-title">{{ $productoVer->pro_nombre }}</div>

                    {{-- ✅ SIN ESTRELLAS / REVIEWS --}}

                    <div class="price mb-3">
                        ${{ number_format((float) $productoVer->pro_precio_venta, 2) }}
                    </div>

                    {{-- ✅ DESCRIPCIÓN DESDE BD --}}
                    <p class="text-muted mb-3">
                        {{ !empty($desc) ? $desc : 'Sin descripción disponible.' }}
                    </p>

                    {{-- ✅ NO mostrar ID / Stock / Estado --}}
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

                    <button class="btn btn-concho product-btn py-3" type="button" disabled>
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
                if(!qtyEl || !btnMinus || !btnPlus) return;

                let q = 1;
                btnMinus.addEventListener('click', () => {
                    q = Math.max(1, q-1);
                    qtyEl.textContent = q;
                });
                btnPlus.addEventListener('click', () => {
                    q = q + 1;
                    qtyEl.textContent = q;
                });
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

                                    {{-- ✅ SIN ESTRELLAS / REVIEWS --}}

                                    <div class="price mb-3">
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
                // ✅ Filtro de PRECIO se mantiene (solo UI local)
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
