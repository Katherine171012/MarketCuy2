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
                    <h2 class="fw-bold text-concho mb-3">Nuestro origen</h2>
                    <p class="text-muted fs-5 mb-3 text-justify" style="text-align: justify;">
                        MarketCuy nació como un proyecto académico que reunió a cuatro estudiantes con visiones complementarias y un objetivo común: transformar la experiencia de compra de supermercado.
                    </p>
                    <p class="text-muted fs-5 mb-3 text-justify" style="text-align: justify;">
                        A través del trabajo colaborativo y la integración de nuestras habilidades individuales, desarrollamos una plataforma que responde a las necesidades reales del consumidor moderno.
                    </p>
                    <p class="text-muted fs-5 mb-0 text-justify" style="text-align: justify;">
                        La sinergia del equipo y el compromiso compartido nos llevó a consolidar este proyecto más allá del ámbito académico.
                        <br><br>
                        Así nació <strong class="text-concho">MarketCuy</strong>: una plataforma de e-commerce desarrollada con dedicación, innovación y enfoque en la excelencia del servicio.
                    </p>
                </div>
            </div>
        </div>

        {{-- QUÉ ES MARKETCUY --}}
        <div class="mt-4 p-4 p-lg-5 bg-white border rounded-4 shadow-sm text-center">
            <h2 class="fw-bold text-concho mb-4">¿Qué es MarketCuy?</h2>
            <div class="mx-auto" style="max-width: 800px;">
                <p class="text-muted fs-5 mb-3">
                    Una plataforma de comercio electrónico especializada en productos de supermercado, diseñada para optimizar la experiencia de compra del usuario.
                </p>
                <p class="text-muted fs-5 mb-3">
                    Nuestro equipo de desarrollo identificó una oportunidad en el mercado: simplificar el proceso de compra de productos de consumo diario mediante tecnología intuitiva y eficiente.
                </p>
                <p class="text-muted fs-5 mb-4">
                    Ofrecemos una interfaz organizada, categorización clara de productos, sistema de entregas confiable y un proceso de verificación de calidad riguroso en cada pedido.
                </p>
                <p class="fs-5 fw-semibold text-concho mb-0">
                    <em>Nuestro principio:</em> "Tecnología al servicio de la comodidad del usuario"
                </p>
                <p class="text-muted small mt-3 mb-0">
                    Comprometidos con la innovación y la satisfacción del cliente.
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
                        Facilitar el acceso a productos de supermercado mediante una plataforma digital eficiente, segura y accesible, garantizando calidad y puntualidad en cada entrega.
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
                        Posicionarnos como una plataforma de referencia en e-commerce de supermercado, reconocida por la confianza, innovación tecnológica y excelencia en el servicio al cliente.
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
                        <li>Excelencia en atención al cliente</li>
                        <li>Innovación continua</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Compromiso --}}
        <div class="mt-4 p-4 p-lg-5 bg-white border rounded-4 shadow-sm">
            <h2 class="fw-bold text-concho mb-3 text-center">Nuestro compromiso</h2>
            <div class="mx-auto" style="max-width: 700px;">
                <p class="text-muted fs-5 mb-0 text-center">
                    Implementamos un riguroso control de calidad en cada etapa del proceso: verificación de productos, empaquetado cuidadoso y supervisión de entregas.
                    <br><br>
                    Nuestro equipo está comprometido con garantizar la satisfacción total del cliente en cada pedido.
                </p>
            </div>
        </div>

        {{-- EL EQUIPO CUY --}}
        <div class="mt-5 text-center">
            <h2 class="fw-bold text-concho mb-2">Nuestro equipo</h2>
            <p class="text-muted mb-5">Profesionales comprometidos con la excelencia</p>

            <div class="row g-4">
                {{-- Kathe --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                        <img src="{{ asset('images/kathe.png') }}"
                             alt="Kathe"
                             class="rounded-circle mb-3 mx-auto d-block"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <h5 class="fw-bold mb-1">Katherine</h5>
                        <p class="text-muted small fst-italic mb-2">Coordinadora de Proyecto</p>
                        <p class="text-muted small mb-0">
                            Responsable de la gestión y coordinación del equipo. Su liderazgo organizado y capacidad de mediación son fundamentales para el funcionamiento eficiente del proyecto.
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
                        <h5 class="fw-bold mb-1">William</h5>
                        <p class="text-muted small fst-italic mb-2">Desarrollador Full Stack</p>
                        <p class="text-muted small mb-0">
                            Especialista en desarrollo de interfaces y experiencia de usuario. Aporta soluciones creativas y mantiene un ambiente de trabajo colaborativo y productivo.
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
                        <h5 class="fw-bold mb-1">Mathias</h5>
                        <p class="text-muted small fst-italic mb-2">Desarrollador Backend</p>
                        <p class="text-muted small mb-0">
                            Enfocado en la arquitectura del sistema y funcionalidades del servidor. Su enfoque dinámico y proactivo impulsa la resolución eficiente de desafíos técnicos.
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
                        <p class="text-muted small fst-italic mb-2">Desarrollador Frontend</p>
                        <p class="text-muted small mb-0">
                            Especialista en diseño e implementación de componentes visuales. Contribuye al equilibrio del equipo con versatilidad técnica y capacidad de adaptación.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
