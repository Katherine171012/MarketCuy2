@extends('layouts.app')
@section('titulo', 'Inicio - MarketCuy')

@section('contenido')

    {{-- 1. HERO SECTION (Igualito al PDF) --}}
    <section class="home-hero">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">MarketCuy - Tu <br> Supermercado en Casa</h1>
            <p class="lead mb-5 fs-4">Productos frescos y de calidad directo a tu puerta</p>

            {{-- Botón a productos.index como pediste --}}
            <a href="{{ route('productos.index') }}" class="btn btn-concho btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                Explorar Productos
            </a>
        </div>
    </section>

    {{-- 2. CATEGORÍAS (Con imágenes, sin íconos) --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-concho mb-5">Nuestras Categorías</h2>

        <div class="row g-4 justify-content-center">
            @foreach($categorias as $cat)
                <div class="col-6 col-md-3">
                    <a href="{{ route('productos.index', ['categoria' => $cat->id_categoria]) }}" class="cat-card d-block">
                        <div class="cat-img-wrap">
                            {{-- Imagen placeholder bonita aleatoria de comida --}}
                            <img src="https://source.unsplash.com/100x100/?food,{{ $cat->cat_nombre }}"
                                 alt="{{ $cat->cat_nombre }}"
                                 onerror="this.src='https://placehold.co/100x100?text={{ substr($cat->cat_nombre, 0, 1) }}'">
                        </div>
                        <span class="fw-bold">{{ $cat->cat_nombre }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. PRODUCTOS DESTACADOS --}}
    <div class="mb-5 bg-light py-5 rounded-4 px-3">
        <h2 class="fw-bold text-concho text-center mb-5">Productos Destacados</h2>

        <div class="row g-4">
            @foreach($productosDestacados as $prod)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="product-card h-100 position-relative">

                        {{-- Lógica Pro Etiqueta (Ej: Oferta, 2x1) --}}
                        @if($prod->pro_etiqueta)
                            <span class="badge-oferta">
                            {{ $prod->pro_etiqueta }}
                        </span>
                        @endif

                        {{-- Imagen --}}
                        <div class="imgwrap">
                            {{-- Validamos si existe imagen, sino una por defecto --}}
                            <img src="{{ $prod->pro_imagen ? asset('img/'.$prod->pro_imagen) : 'https://placehold.co/400x300?text=Sin+Imagen' }}"
                                 alt="{{ $prod->pro_nombre }}">
                        </div>

                        <div class="p-3">
                            {{-- Categoría pequeña --}}
                            <div class="badge-cat mb-2">
                                {{ $prod->categoria->cat_nombre ?? 'General' }}
                            </div>

                            {{-- Nombre --}}
                            <h5 class="product-title text-truncate" title="{{ $prod->pro_nombre }}">
                                {{ $prod->pro_nombre }}
                            </h5>

                            {{-- Estrellas (Estáticas por diseño) --}}
                            <div class="stars mb-2">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <span class="text-muted ms-1 small">(12)</span>
                            </div>

                            {{-- Precios con lógica de "Antes" --}}
                            <div class="d-flex align-items-center mb-3">
                                <span class="price">${{ number_format($prod->pro_precio_venta, 2) }}</span>

                                {{-- Lógica Precio Antes (Tachado) --}}
                                @if($prod->pro_precio_anterior && $prod->pro_precio_anterior > $prod->pro_precio_venta)
                                    <span class="precio-antes">
                                    ${{ number_format($prod->pro_precio_anterior, 2) }}
                                </span>
                                @endif
                            </div>

                            {{-- Botón que activa el Click Count --}}
                            <a href="{{ route('producto.click', $prod->id_producto) }}"
                               class="btn btn-concho product-btn">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 4. CARACTERÍSTICAS (Iconos inferiores) --}}
    <div class="py-5">
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
