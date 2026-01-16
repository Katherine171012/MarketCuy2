@extends('layouts.app')

@section('titulo', 'Contacto')

@section('contenido')

    {{-- HERO CONTACTO CON IMAGEN DE FONDO --}}
    <section class="hero-contacto" style="background-image: url('{{ asset('images/hero-contacto1.png') }}');">
        <div class="hero-overlay"></div>
        <div class="hero-inner">
            <div class="container">
                <div class="hero-content">
                    <h1>¿Cómo podemos ayudarte?</h1>
                    <p>
                        Estamos aquí para resolver tus dudas sobre productos,
                    <p>
                        pedidos y tu experiencia de compra en MarketCuy.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTENEDOR PRINCIPAL CON PADDING --}}
    <div class="contacto-container">
        <div class="container">

            {{-- MENSAJE DE ÉXITO --}}
            @if(session('contacto_ok'))
                <div class="alert alert-success alert-soft shadow-sm">
                    <div class="fw-bold mb-1">✅ ¡Mensaje enviado correctamente!</div>
                    <div>
                        Gracias por contactarnos, <b>{{ session('contacto_nombre') }}</b>.
                        Hemos recibido tu mensaje y te responderemos a la brevedad posible.
                    </div>
                    <div class="mt-2 small text-muted">
                        Número de referencia: <b>{{ session('contacto_ref') }}</b>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('contacto.index') }}" class="btn btn-outline-concho fw-bold">
                            Enviar otro mensaje
                        </a>
                    </div>
                </div>
            @endif

            {{-- ERRORES --}}
            @if($errors->any())
                <div class="alert alert-danger alert-soft shadow-sm">
                    <div class="fw-bold mb-2">Revisa estos campos:</div>
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SECCIÓN 1: CANALES DE CONTACTO --}}
            <section class="seccion-canales">
                <div class="text-center mb-4">
                    <h2 class="titulo-seccion">Contáctanos</h2>
                    <p class="subtitulo-seccion">Elige el canal que prefieras para comunicarte con nosotros</p>
                </div>

                <div class="row g-3 g-md-4">
                    <div class="col-12 col-md-4">
                        <div class="bg-white border rounded-4 shadow-sm p-4 h-100 contacto-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-concho text-white d-grid"
                                     style="width:48px;height:48px;place-items:center;">
                                    <i class="fa-solid fa-envelope fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Correo Electrónico</div>
                                    <div class="text-muted small">atencion@marketcuy.com</div>
                                </div>
                            </div>
                            <div class="text-muted small mt-3">
                                Escríbenos tus consultas y sugerencias.
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="bg-white border rounded-4 shadow-sm p-4 h-100 contacto-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-concho text-white d-grid"
                                     style="width:48px;height:48px;place-items:center;">
                                    <i class="fa-solid fa-phone fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Teléfono</div>
                                    <div class="text-muted small">(02) 234-5678</div>
                                </div>
                            </div>
                            <div class="text-muted small mt-3">
                                Atención de lunes a viernes<br>08:00 - 18:00
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="bg-white border rounded-4 shadow-sm p-4 h-100 contacto-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-concho text-white d-grid"
                                     style="width:48px;height:48px;place-items:center;">
                                    <i class="fa-brands fa-whatsapp fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">WhatsApp</div>
                                    <div class="text-muted small">+593 99 123 4567</div>
                                </div>
                            </div>
                            <div class="text-muted small mt-3">
                                Chatea con nosotros.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SECCIÓN 2: FORMULARIO DE CONTACTO --}}
            <section class="seccion-formulario">
                <div class="bg-white border rounded-4 shadow-sm p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="titulo-seccion mb-2">Envíanos un mensaje</h2>
                        <p class="subtitulo-seccion mb-0">
                            Completa el formulario y nos pondremos en contacto contigo lo antes posible
                        </p>
                    </div>

                    <form method="POST" action="{{ route('contacto.store') }}" id="frmContacto" novalidate>
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nombre completo *</label>
                                <input type="text" class="form-control" name="con_nombre" id="con_nombre"
                                       value="{{ old('con_nombre') }}" placeholder="Ej: María García">
                                <div class="invalid-feedback" id="err_nombre">El nombre debe tener al menos 3 caracteres.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Correo electrónico *</label>
                                <input type="email" class="form-control" name="con_correo" id="con_correo"
                                       value="{{ old('con_correo') }}" placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback" id="err_correo">Ingresa un correo válido.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Teléfono (opcional)</label>
                                <input type="tel" class="form-control" name="con_telefono" id="con_telefono"
                                       value="{{ old('con_telefono') }}" placeholder="0987654321">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold mb-2">Tipo de consulta *</label>

                                <div class="d-flex flex-wrap gap-2" id="tipoWrap">
                                    <button type="button" class="btn btn-outline-concho tipo-btn" data-tipo="productos">
                                        🛍️ Productos
                                    </button>
                                    <button type="button" class="btn btn-outline-concho tipo-btn" data-tipo="pedidos">
                                        📦 Pedidos
                                    </button>
                                    <button type="button" class="btn btn-outline-concho tipo-btn" data-tipo="pagos">
                                        💳 Pagos
                                    </button>
                                    <button type="button" class="btn btn-outline-concho tipo-btn" data-tipo="sugerencias">
                                        💡 Sugerencias
                                    </button>
                                </div>

                                <input type="hidden" name="con_tipo" id="con_tipo" value="{{ old('con_tipo') }}">
                                <div class="text-danger small mt-1 d-none" id="err_tipo">Selecciona un tipo de consulta.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Mensaje *</label>
                                <textarea class="form-control" name="con_mensaje" id="con_mensaje" rows="5"
                                          placeholder="Escribe tu mensaje aquí...">{{ old('con_mensaje') }}</textarea>

                                <div class="d-flex justify-content-between mt-1">
                                    <div class="invalid-feedback d-block d-none" id="err_mensaje">
                                        El mensaje debe tener al menos 10 caracteres.
                                    </div>
                                    <div class="small text-muted ms-auto" id="countMsg">0 / 500</div>
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-center justify-content-md-start">
                                <button type="submit" class="btn btn-concho fw-bold px-5" id="btnEnviar">
                                    Enviar mensaje →
                                </button>
                                <a href="{{ url('/') }}" class="btn btn-outline-concho fw-bold px-4">
                                    Volver al inicio
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            {{-- SECCIÓN 3: PREGUNTAS FRECUENTES --}}
            <section class="seccion-faqs">
                <div class="text-center mb-4">
                    <h2 class="titulo-seccion">Preguntas Frecuentes</h2>
                    <p class="subtitulo-seccion">Encuentra respuestas rápidas a las consultas más comunes</p>
                </div>

                <div class="accordion" id="faqContacto">
                    @php
                        $faqs = [
                            ['q' => '¿Qué métodos de pago aceptan?', 'a' => 'Aceptamos tarjetas de crédito/débito Visa y Mastercard, transferencias bancarias y pagos contra entrega.'],
                            ['q' => '¿Cómo realizo un pedido?', 'a' => 'Explora nuestro catálogo de productos, agrégalos al carrito y completa el proceso de compra con tus datos de entrega.'],
                            ['q' => '¿Cuál es la zona de cobertura?', 'a' => 'Realizamos entregas en toda la ciudad de Quito y valles aledaños (Cumbayá, Tumbaco, Los Chillos).'],
                            ['q' => '¿Puedo modificar mi pedido?', 'a' => 'Contáctanos inmediatamente vía WhatsApp o correo para verificar si tu pedido aún puede ser modificado.'],
                            ['q' => '¿Cuánto tiempo tarda la entrega?', 'a' => 'Las entregas se realizan en un plazo de 24 a 48 horas hábiles según tu ubicación.'],
                            ['q' => '¿Los precios incluyen IVA?', 'a' => 'Sí, todos los precios mostrados incluyen IVA.'],
                        ];
                    @endphp

                    @foreach($faqs as $i => $f)
                        <div class="accordion-item mb-2 border rounded-3 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button @if($i>0) collapsed @endif fw-semibold"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faq{{ $i }}">
                                    {{ $f['q'] }}
                                </button>
                            </h2>
                            <div id="faq{{ $i }}"
                                 class="accordion-collapse collapse @if($i===0) show @endif"
                                 data-bs-parent="#faqContacto">
                                <div class="accordion-body text-muted">
                                    {{ $f['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>

@endsection
