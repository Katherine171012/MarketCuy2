@extends('layouts.app')

@section('titulo', 'Nosotros - MarketCuy')

@section('contenido')

    {{-- HERO NOSOTROS (texto abajo a la derecha) --}}
    <section class="home-hero nosotros-hero">
        <div class="container text-white text-end nosotros-hero-content">
            <h1 class="display-4 fw-bold mb-2">Nuestra historia</h1>
        </div>
    </section>


    {{-- CONTENIDO --}}
    <div class="container my-5">

        {{-- CÓMO NACIÓ MARKETCUY --}}
        <div class="p-4 p-lg-5 bg-light rounded-4 shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-5">
                    <img src="{{ asset('images/historia-nosotros.png') }}"
                         alt="Historia MarketCuy"
                         class="img-fluid rounded-4 shadow-sm">
                </div>
                <div class="col-12 col-lg-7">
                    <h2 class="fw-bold text-concho mb-3">De grupo random a familia cuy</h2>
                    <p class="text-muted fs-5 mb-3 text-justify" style="text-align: justify;">
                        Todo empezó con un proyecto en grupo. Nos juntamos porque éramos la misma cosa: cuatro cuys con vibras parecidas pero personalidades totalmente diferentes.
                    </p>
                    <p class="text-muted fs-5 mb-3 text-justify" style="text-align: justify;">
                        Entre discusiones, dramas y peleas que parecían el fin del mundo (pero eran por tonterías), descubrimos algo: cada uno tenía su chispa. Y que juntos, éramos imparables (bueno, casi).
                    </p>
                    <p class="text-muted fs-5 mb-0 text-justify" style="text-align: justify;">
                        Al final del proyecto, decidimos quedarnos como equipo para siempre.
                        <br><br>
                        Así nació <strong class="text-concho">MarketCuy</strong>: cuatro cuys que aprendieron que los mejores equipos no se arman... se construyen a base de paciencia, risas y amor.
                    </p>
                </div>
            </div>
        </div>

        {{-- QUÉ ES MARKETCUY --}}
        <div class="mt-4 p-4 p-lg-5 bg-white border rounded-4 shadow-sm text-center">
            <h2 class="fw-bold text-concho mb-4">¿Y qué es MarketCuy?</h2>
            <div class="mx-auto" style="max-width: 800px;">
                <p class="text-muted fs-5 mb-3">
                    Un e-commerce de supermercado hecho por cuys, para humanos que odian hacer fila.
                </p>
                <p class="text-muted fs-5 mb-3">
                    Somos cuatro programadores que dijimos: "¿Y si hacer mercado fuera tan fácil como pedir pizza?"
                </p>
                <p class="text-muted fs-5 mb-4">
                    Aquí no hay pasillos confusos ni productos escondidos. Solo categorías claras, entregas puntuales, y un equipo que revisa cada pedido como si fuera para su mamá.
                </p>
                <p class="fs-5 fw-semibold text-concho mb-0">
                     <em>Nuestro lema:</em> "Si un cuy puede organizarse, tú también" (pero con nuestra ayuda)
                </p>
                <p class="text-muted small mt-3 mb-0">
                    Porque sí, somos cuys. Pero somos cuys comprometidos.
                </p>
            </div>
        </div>

        {{-- Misión / Visión / Valores --}}
        <div class="row g-4 mt-4">
            <div class="col-12 col-md-4">
                <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                    <div class="mb-3">
                        <i class="fa-solid fa-bullseye fs-1 text-concho"></i>
                    </div>
                    <h4 class="fw-bold">Misión</h4>
                    <p class="text-muted small mb-0">
                        Brindar una experiencia de compra online eficiente, segura y accesible, conectando productos de calidad con tu hogar.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                    <div class="mb-3">
                        <i class="fa-solid fa-eye fs-1 text-concho"></i>
                    </div>
                    <h4 class="fw-bold">Visión</h4>
                    <p class="text-muted small mb-0">
                        Ser una plataforma referente de supermercado online por confianza, servicio y mejora continua.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                    <div class="mb-3">
                        <i class="fa-solid fa-handshake fs-1 text-concho"></i>
                    </div>
                    <h4 class="fw-bold">Valores</h4>
                    <ul class="text-muted small mb-0 ps-3">
                        <li>Calidad y frescura</li>
                        <li>Transparencia</li>
                        <li>Responsabilidad</li>
                        <li>Atención al cliente</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Compromiso --}}
        <div class="mt-4 p-4 p-lg-5 bg-white border rounded-4 shadow-sm">
            <h2 class="fw-bold text-concho mb-3 text-center">Nuestro compromiso</h2>
            <div class="mx-auto" style="max-width: 700px;">
                <p class="text-muted fs-5 mb-0 text-center">
                    Entre peleas y risas, nos ponemos serios con una cosa: tu pedido. Lo revisamos todo, empacamos con cuidado, y nos aseguramos de que llegue completo.
                    <br><br>
                    Porque si algo sale mal, Kathe nos regaña a todos
                </p>
            </div>
        </div>

        {{-- EL EQUIPO CUY --}}
        <div class="mt-5 text-center">
            <h2 class="fw-bold text-concho mb-2">Conoce a los cuys detrás del teclado</h2>
            <p class="text-muted mb-5">Cuatro personalidades, un caos funcional</p>

            <div class="row g-4">
                {{-- Kathe --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                        <img src="{{ asset('images/kathe.png') }}"
                             alt="Kathe"
                             class="rounded-circle mb-3 mx-auto d-block"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <h5 class="fw-bold mb-1">Kathe</h5>
                        <p class="text-muted small fst-italic mb-2">"Líder del Grupo"</p>
                        <p class="text-muted small mb-0">
                            Sin ella, no seríamos nada. Literalmente no funcionaríamos. Es la única tranquila del grupo y nos tiene una paciencia que nadie más tendría. Ella nos sostiene.
                        </p>
                    </div>
                </div>

                {{-- Will --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                        <img src="{{ asset('images/will.png') }}"
                             alt="Will"
                             class="rounded-circle mb-3 mx-auto d-block"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <h5 class="fw-bold mb-1">Will</h5>
                        <p class="text-muted small fst-italic mb-2">"Patico"</p>
                        <p class="text-muted small mb-0">
                            No pelea, puro chiste. El que hace alegría al grupo y siempre sale con huevadas. Si hay tensión, Will la rompe con algo random. Energía positiva 24/7.
                        </p>
                    </div>
                </div>

                {{-- Mathi --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                        <img src="{{ asset('images/mathi.png') }}"
                             alt="Mathi"
                             class="rounded-circle mb-3 mx-auto d-block"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <h5 class="fw-bold mb-1">Mathi</h5>
                        <p class="text-muted small fst-italic mb-2">"Sopa"</p>
                        <p class="text-muted small mb-0">
                            El que nunca se calla, nunca se queda quieto y siempre está peleando. Caos puro, pero cuando se pone a trabajar, lo hace bien. Funcional a su manera.
                        </p>
                    </div>
                </div>

                {{-- Flores --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                        <img src="{{ asset('images/flores.png') }}"
                             alt="Flores"
                             class="rounded-circle mb-3 mx-auto d-block"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <h5 class="fw-bold mb-1">Flores</h5>
                        <p class="text-muted small fst-italic mb-2">"Varón"</p>
                        <p class="text-muted small mb-0">
                            Siempre con un chiste o una huevada lista. Con Mathi en las peleas, con Will en las risas. La que mantiene la vibra ligera cuando el grupo se pone denso.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
