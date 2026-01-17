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

        <div class="text-muted small mb-3">
            Inicio &nbsp;›&nbsp; Productos &nbsp;›&nbsp;
            <span class="fw-bold">{{ $productoVer->pro_categoria ?? 'Sin categoría' }}</span>
            &nbsp;›&nbsp; {{ $productoVer->pro_descripcion }}
        </div>

        @php
            // rating visual (fake pero estable por ID, solo UI)
            $seed = intval(preg_replace('/\D/', '', $productoVer->id_producto)) ?: 1;
            $rating = 3.8 + (($seed % 13) / 20); // 3.8 - 4.45
            $reviews = 120 + ($seed % 800);
            $cat = $productoVer->pro_categoria ?? 'Productos';
        @endphp

        <div class="detail-card p-4">
            <div class="row g-4 align-items-start">
                <div class="col-lg-6">
                    @if(!empty($productoVer->pro_imagen))
                        {{-- FIX: tu BD guarda "productos/P1000.jpg" -> asset('storage/' . ...) --}}
                        <img class="detail-img rounded-4"
                             src="{{ asset('storage/' . $productoVer->pro_imagen) }}"
                             alt="Imagen {{ $productoVer->pro_descripcion }}">
                    @else
                        <div class="detail-img rounded-4 d-flex align-items-center justify-content-center text-muted">
                            Sin imagen
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <span class="badge-cat">{{ $cat }}</span>

                    <div class="detail-title">{{ $productoVer->pro_descripcion }}</div>

                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="stars">
                            @for($i=1; $i<=5; $i++)
                                @if($rating >= $i)
                                    <i class="fa-solid fa-star"></i>
                                @elseif($rating >= ($i-0.5))
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                @else
                                    <i class="fa-regular fa-star"></i>
                                @endif
                            @endfor
                        </span>
                        <span class="small text-muted">
                            {{ number_format($rating, 1) }} ({{ $reviews }} reviews)
                        </span>
                    </div>

                    <div class="price mb-3">
                        ${{ number_format((float) $productoVer->pro_precio_venta, 2) }}
                    </div>

                    <p class="text-muted mb-3">
                        Producto registrado en el inventario. Categoría: <b>{{ $cat }}</b>.
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="pill w-100">
                                <span class="text-muted">ID</span><br>
                                {{ $productoVer->id_producto }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pill w-100">
                                <span class="text-muted">Stock</span><br>
                                {{ $productoVer->pro_saldo_final }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pill w-100">
                                <span class="text-muted">Unidad</span><br>
                                {{ $productoVer->pro_um_compra }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pill w-100">
                                <span class="text-muted">Estado</span><br>
                                {{ $productoVer->estado_prod === 'ACT' ? 'Activo' : 'Inactivo' }}
                            </div>
                        </div>
                    </div>

                    {{-- cantidad demo (sin backend) --}}
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

        <div class="detail-card p-4 mt-4">
            <ul class="nav nav-tabs" id="tabDetalle" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">
                        Descripción
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="nutri-tab" data-bs-toggle="tab" data-bs-target="#nutri" type="button" role="tab">
                        Información Nutricional
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="rev-tab" data-bs-toggle="tab" data-bs-target="#rev" type="button" role="tab">
                        Reviews
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3">
                <div class="tab-pane fade show active" id="desc" role="tabpanel">
                    <p class="mb-0">
                        {{ $productoVer->pro_descripcion }}. Producto en categoría <b>{{ $cat }}</b>.
                    </p>
                </div>
                <div class="tab-pane fade" id="nutri" role="tabpanel">
                    <p class="mb-0 text-muted">
                        (Demo) Este módulo no maneja nutrición. Aquí iría la ficha nutricional si existiera en tu BD.
                    </p>
                </div>
                <div class="tab-pane fade" id="rev" role="tabpanel">
                    <p class="mb-0 text-muted">
                        (Demo) Reviews simuladas para diseño.
                    </p>
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

        {{-- MODO LISTADO (TIENDA) --}}
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

                    {{-- (Tu CRUD sigue existiendo, lo dejo oculto para que no dañe el look tienda)
                         Si quieres verlo, quita d-none --}}
                    <div class="d-none">
                        <a class="btn btn-primary"
                           href="{{ route('productos.index', ['create' => 1]) }}">
                            + Crear nuevo producto
                        </a>
                    </div>
                </div>

                {{-- cards --}}
                <div class="row g-4" id="gridProductos">
                    @foreach($productos as $p)
                        @php
                            $seed = intval(preg_replace('/\D/', '', $p->id_producto)) ?: 1;
                            $rating = 3.7 + (($seed % 13) / 20);
                            $reviews = 90 + ($seed % 900);

                            $cat = $p->pro_categoria ?? 'Productos';
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4 producto-item"
                             data-nombre="{{ strtolower($p->pro_descripcion) }}"
                             data-precio="{{ floatval($p->pro_precio_venta) }}"
                             data-cat="{{ strtolower($cat) }}">
                            <div class="product-card h-100">
                                <div class="imgwrap">
                                    @if(!empty($p->pro_imagen))
                                        {{-- FIX: BD guarda "productos/P1000.jpg" --}}
                                        <img src="{{ asset('storage/' . $p->pro_imagen) }}"
                                             alt="Imagen {{ $p->pro_descripcion }}">
                                    @else
                                        <div class="h-100 d-flex align-items-center justify-content-center text-muted small">
                                            Sin imagen
                                        </div>
                                    @endif
                                </div>

                                <div class="p-3">
                                    <span class="badge-cat">{{ $cat }}</span>

                                    <div class="product-title">{{ $p->pro_descripcion }}</div>

                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="stars">
                                            @for($i=1; $i<=5; $i++)
                                                @if($rating >= $i)
                                                    <i class="fa-solid fa-star"></i>
                                                @elseif($rating >= ($i-0.5))
                                                    <i class="fa-solid fa-star-half-stroke"></i>
                                                @else
                                                    <i class="fa-regular fa-star"></i>
                                                @endif
                                            @endfor
                                        </span>
                                        <span class="small text-muted">({{ $reviews }})</span>
                                    </div>

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

                {{-- paginación --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $productos->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>

        {{-- filtros locales (buscar + slider precio) sin backend --}}
        <script>
            (function(){
                const txt = document.getElementById('txtBuscarLocal');
                const range = document.getElementById('rangePrecio');
                const lbl = document.getElementById('lblPrecio');
                const items = Array.from(document.querySelectorAll('.producto-item'));

                function aplicar(){
                    const q = (txt?.value || '').trim().toLowerCase();
                    const max = range ? parseFloat(range.value || '999999') : Infinity;

                    items.forEach(el => {
                        const nombre = el.getAttribute('data-nombre') || '';
                        const precio = parseFloat(el.getAttribute('data-precio') || '0');

                        const okNombre = !q || nombre.includes(q);
                        const okPrecio = precio <= max;

                        el.style.display = (okNombre && okPrecio) ? '' : 'none';
                    });

                    if(lbl && range){
                        lbl.textContent = 'Hasta $' + parseFloat(range.value || '0').toFixed(2);
                    }
                }

                if(txt) txt.addEventListener('input', aplicar);
                if(range) range.addEventListener('input', aplicar);

                aplicar();
            })();
        </script>

    @endif
@endsection
