@extends('layouts.app')

@section('titulo', 'Inicio - MarketCuy')

@section('contenido')

    {{-- 1. HERO SECTION --}}
    <section class="home-hero">
        <div class="container text-center text-white">
            <h1 class="display-3 fw-bold mb-3">MarketCuy - Tu <br> Supermercado en Casa</h1>
            <p class="lead mb-5 fs-4 text-light">Productos frescos y de calidad directo a tu puerta</p>

            <a href="{{ route('productos.index') }}" class="btn btn-concho btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                Explorar Productos
            </a>
        </div>
    </section>

    {{-- 2. CATEGORÍAS (Estilo Rectangular Compacto) --}}
    <div class="container my-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-concho">Categorías</h2>
        </div>

        <div class="row g-3 justify-content-center">
            @foreach($categorias as $cat)
                {{--
                   COLUMNAS:
                   col-6: 2 por fila en celular (pequeñas)
                   col-md-3: 4 por fila en tablet
                   col-lg-2: 6 por fila en PC (compactas)
                --}}
                <div class="col-6 col-md-3 col-lg-3">

                    <a href="{{ route('productos.index', ['categoria' => $cat->id_categoria]) }}"
                       class="cat-tile shadow-sm">

                        {{-- Imagen --}}
                        <img src="{{ asset('storage/categorias/categoria' . $cat->id_categoria . '.jpg') }}"
                             alt="{{ $cat->cat_nombre }}"
                             onerror="this.style.display='none';">
                        {{-- Si falla la imagen, la ocultamos para ver el fondo de color --}}

                        {{-- Texto centrado --}}
                        <div class="cat-overlay">
                            <span class="cat-name">{{ $cat->cat_nombre }}</span>
                        </div>
                    </a>

                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. PRODUCTOS DESTACADOS --}}
    <div class="mb-5 bg-light py-5 rounded-4 px-3">
        <div class="container">
            <h2 class="fw-bold text-concho text-center mb-5">Productos Destacados</h2>

            <div class="row g-4">
                @foreach($productosDestacados as $prod)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="product-card h-100 position-relative bg-white rounded-3 shadow-sm border">

                            {{-- Etiqueta Oferta / Top Ventas (campo pro_etiqueta) --}}
                            @if($prod->pro_etiqueta)
                                <span class="badge-oferta">
                                    {{ $prod->pro_etiqueta }}
                                </span>
                            @endif

                            {{-- Imagen del producto --}}
                            <div class="imgwrap position-relative overflow-hidden" style="height: 200px;">
                                <img src="{{ $prod->pro_imagen ? asset('storage/'.$prod->pro_imagen) : 'https://placehold.co/400x300?text=Sin+Imagen' }}"
                                     alt="{{ $prod->pro_nombre }}"
                                     class="w-100 h-100 object-fit-cover"
                                     onerror="this.src='https://placehold.co/400x300?text=Sin+Foto'">
                            </div>

                            <div class="p-3">
                                {{-- Nombre Categoría --}}
                                <div class="badge-cat mb-2 text-primary bg-primary-subtle px-2 py-1 rounded-pill d-inline-block small fw-bold">
                                    {{ $prod->categoria->cat_nombre ?? 'General' }}
                                </div>

                                {{-- Nombre Producto --}}
                                <h5 class="product-title text-truncate fw-bold text-dark mt-2" title="{{ $prod->pro_nombre }}">
                                    {{ $prod->pro_nombre }}
                                </h5>

                                {{--
                                    AQUÍ QUITAMOS LAS ESTRELLAS Y EL CONTADOR VISUAL.
                                    Ya no hay nada entre el título y el precio.
                                --}}

                                {{-- Sección de Precios --}}
                                <div class="d-flex align-items-center mb-3 mt-3">
                                    {{-- Precio Actual (Grande) --}}
                                    <span class="price fs-5 fw-bold text-concho">
                                        ${{ number_format($prod->pro_precio_venta, 2) }}
                                    </span>

                                    {{-- Precio Antes (Tachado) - Usamos el método del modelo --}}
                                    @if($prod->tieneDescuento())
                                        <span class="precio-antes ms-2 text-muted text-decoration-line-through small">
                                            ${{ number_format($prod->pro_precio_antes, 2) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Botón de Acción --}}
                                {{-- Al hacer clic, va al controlador, suma +1 al click_count y redirige --}}
                                <a href="{{ route('producto.click', $prod->id_producto) }}"
                                   class="btn btn-concho w-100 fw-bold">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 4. CARACTERÍSTICAS --}}
    <div class="container py-5">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <i class="fa-solid fa-truck fs-1 text-danger mb-3"></i>
                <h4 class="fw-bold">Envío a Domicilio</h4>
                <p class="text-muted small">Recibe tus productos frescos en la puerta de tu casa</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-leaf fs-1 text-success mb-3"></i>
                <h4 class="fw-bold">Productos Frescos</h4>
                <p class="text-muted small">Selección diaria de los mejores productos del mercado</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-tag fs-1 text-concho mb-3"></i>
                <h4 class="fw-bold">Precios Bajos</h4>
                <p class="text-muted small">Ofertas y promociones todos los días del año</p>
            </div>
        </div>
    </div>

@endsection
