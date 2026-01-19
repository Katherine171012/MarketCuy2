@extends('layouts.app')

@section('titulo', 'Contacto')

@section('contenido')

    {{-- HERO CONTACTO CON IMAGEN DE FONDO --}}
    <section class="hero-contacto" style="background-image: url('{{ asset('images/hero-contacto1.png') }}');">
        <div class="hero-overlay"></div>
        <div class="hero-inner">
            <div class="container">
                <div class="hero-content">
                    <h1>¿Necesitas ayuda?</h1>
                    <p>Tu experiencia en MarketCuy es nuestra prioridad.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="contacto-wrapper">
        <div class="container">

            {{-- MENSAJE DE ÉXITO --}}
            @if(session('contacto_ok'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-5" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-check fs-3 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading fw-bold mb-2">¡Mensaje enviado correctamente!</h5>
                            <p class="mb-2">
                                Gracias por contactarnos, <strong>{{ session('contacto_nombre') }}</strong>.
                                Hemos recibido tu mensaje y te responderemos a la brevedad posible.
                            </p>
                            <a href="{{ route('contacto.index') }}" class="btn btn-outline-success btn-sm fw-bold">
                                Enviar otro mensaje
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ERRORES --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-5" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-triangle-exclamation fs-3 text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading fw-bold mb-2">Por favor revisa estos campos:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="text-center mb-5">
                <h1 class="titulo-formulario">¿Cómo prefieres contactarnos?</h1>
                <p class="subtitulo-formulario">
                    Completa el formulario o escríbenos directamente
                </p>
            </div>

            {{-- SECCIÓN PRINCIPAL: 2 COLUMNAS --}}
            <section class="seccion-principal-contacto">
                <div class="row g-4 align-items-start">
                    {{-- SECCIÓN PRINCIPAL: 2 COLUMNAS --}}
                    <section class="seccion-principal-contacto">
                        <div class="row g-4 align-items-start">

                            {{-- COLUMNA IZQUIERDA: FORMULARIO --}}
                            <div class="col-12 col-lg-8">
                                <div class="tarjeta-formulario">
                                    <div class="mb-4">
                                        <h2 class="titulo-formulario">Envíanos un Mensaje</h2>
                                        <p class="subtitulo-formulario">
                                            Completa el formulario y nos pondremos en contacto contigo lo antes posible
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('contacto.store') }}" id="frmContacto" novalidate>
                                        @csrf

                                        <div class="row g-4">
                                            {{-- NOMBRE --}}
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold text-dark">
                                                    Nombre completo <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                       class="form-control form-control-contacto"
                                                       name="con_nombre"
                                                       id="con_nombre"
                                                       value="{{ old('con_nombre') }}"
                                                       placeholder="Ej: María García">
                                                <div class="invalid-feedback" id="err_nombre">
                                                    El nombre debe tener al menos 3 caracteres.
                                                </div>
                                            </div>

                                            {{-- EMAIL --}}
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold text-dark">
                                                    Correo electrónico <span class="text-danger">*</span>
                                                </label>
                                                <input type="email"
                                                       class="form-control form-control-contacto"
                                                       name="con_correo"
                                                       id="con_correo"
                                                       value="{{ old('con_correo') }}"
                                                       placeholder="ejemplo@correo.com">
                                                <div class="invalid-feedback" id="err_correo">
                                                    Ingresa un correo válido.
                                                </div>
                                            </div>

                                            {{-- TELÉFONO --}}
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold text-dark">
                                                    Teléfono <span class="text-muted small">(opcional)</span>
                                                </label>
                                                <input type="tel"
                                                       class="form-control form-control-contacto"
                                                       name="con_telefono"
                                                       id="con_telefono"
                                                       value="{{ old('con_telefono') }}"
                                                       placeholder="0987654321">
                                            </div>

                                            {{-- TIPO DE CONSULTA --}}
                                            <div class="col-12">
                                                <label class="form-label fw-semibold text-dark mb-3">
                                                    Tipo de consulta <span class="text-danger">*</span>
                                                </label>

                                                <div class="tipo-consulta-wrapper">
                                                    <button type="button" class="tipo-btn" data-tipo="productos">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                        <span>Productos</span>
                                                    </button>
                                                    <button type="button" class="tipo-btn" data-tipo="pedidos">
                                                        <i class="fa-solid fa-box"></i>
                                                        <span>Pedidos</span>
                                                    </button>
                                                    <button type="button" class="tipo-btn" data-tipo="pagos">
                                                        <i class="fa-solid fa-credit-card"></i>
                                                        <span>Pagos</span>
                                                    </button>
                                                    <button type="button" class="tipo-btn" data-tipo="sugerencias">
                                                        <i class="fa-solid fa-lightbulb"></i>
                                                        <span>Sugerencias</span>
                                                    </button>
                                                </div>

                                                <input type="hidden" name="con_tipo" id="con_tipo" value="{{ old('con_tipo') }}">
                                                <div class="text-danger small mt-2 d-none" id="err_tipo">
                                                    Selecciona un tipo de consulta.
                                                </div>
                                            </div>

                                            {{-- MENSAJE --}}
                                            <div class="col-12">
                                                <label class="form-label fw-semibold text-dark">
                                                    Mensaje <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control form-control-contacto"
                                                          name="con_mensaje"
                                                          id="con_mensaje"
                                                          rows="6"
                                                          placeholder="Escribe tu mensaje aquí...">{{ old('con_mensaje') }}</textarea>

                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <div class="invalid-feedback d-block d-none" id="err_mensaje">
                                                        El mensaje debe tener entre 10 y 500 caracteres.
                                                    </div>
                                                    <div class="small text-muted ms-auto" id="countMsg">0 / 500</div>
                                                </div>
                                            </div>

                                            {{-- BOTONES --}}
                                            <div class="col-12">
                                                <div>
                                                    <button type="submit" class="btn btn-concho btn-lg fw-bold px-5" id="btnEnviar">
                                                        <i class="fa-solid fa-paper-plane me-2"></i>
                                                        Enviar mensaje
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- COLUMNA DERECHA: INFORMACIÓN --}}
                            <div class="col-12 col-lg-4">
                                {{-- TARJETA INFORMACIÓN DE CONTACTO --}}
                                <div class="tarjeta-info-contacto mb-4">
                                    <h3 class="titulo-info-contacto mb-4">Información de Contacto</h3>

                                    <div class="info-item">
                                        <div class="info-icono">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="info-contenido">
                                            <div class="info-titulo">Email</div>
                                            <div class="info-texto">contacto@marketcuy.com</div>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-icono">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="info-contenido">
                                            <div class="info-titulo">Teléfono</div>
                                            <div class="info-texto">+593 983417501</div>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-icono">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="info-contenido">
                                            <div class="info-titulo">Dirección</div>
                                            <div class="info-texto">Av. 12 de Octubre y Carrión</div>
                                        </div>
                                    </div>

                                    <div class="info-item mb-0">
                                        <div class="info-icono">
                                            <i class="fa-solid fa-clock"></i>
                                        </div>
                                        <div class="info-contenido">
                                            <div class="info-titulo">Horario de Respuesta</div>
                                            <div class="info-texto">
                                                Lunes a Domingo<br>
                                                8:00 AM - 10:00 PM
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- BLOQUE ¿NECESITAS AYUDA? --}}
                                <div class="bloque-ayuda">
                                    <div class="ayuda-icono-grande mb-3">
                                        <i class="fa-solid fa-headset"></i>
                                    </div>
                                    <h4 class="ayuda-titulo">¿Necesitas ayuda?</h4>
                                    <p class="ayuda-texto">
                                        Nuestro equipo está aquí para ayudarte
                                    </p>
                                    <div class="ayuda-contactos">
                                        <a href="tel:+593983417501" class="ayuda-boton" title="Llamar">
                                            <i class="fa-solid fa-phone"></i>
                                        </a>
                                        <a href="https://wa.me/593983417501?text=Hola,%20necesito%20ayuda%20con%20MarketCuy" class="ayuda-boton" target="_blank" title="WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                        <a href="mailto:esflores@puce.edu.ec" class="ayuda-boton" title="Email">
                                            <i class="fa-solid fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    {{-- SECCIÓN MAPA --}}
                    <section class="seccion-mapa">
                        <div class="container">
                            <div class="text-center mb-5">
                                <h2 class="titulo-mapa">Encuéntranos</h2>
                                <p class="subtitulo-mapa">Visítanos en nuestra ubicación en Quito</p>
                            </div>

                            <div class="row g-4 align-items-center">
                                {{-- INFO UBICACIÓN --}}
                                <div class="col-12 col-lg-4">
                                    <div class="tarjeta-ubicacion">
                                        <div class="ubicacion-item">
                                            <div class="ubicacion-icono">
                                                <i class="fa-solid fa-map-marker-alt"></i>
                                            </div>
                                            <div>
                                                <h5 class="ubicacion-titulo">Dirección</h5>
                                                <p class="ubicacion-texto">
                                                    Av. 12 de Octubre y Carrión<br>
                                                    Quito, Ecuador
                                                </p>
                                            </div>
                                        </div>

                                        <div class="ubicacion-item mb-0">
                                            <div class="ubicacion-icono">
                                                <i class="fa-solid fa-clock"></i>
                                            </div>
                                            <div>
                                                <h5 class="ubicacion-titulo">Horario de Atención</h5>
                                                <p class="ubicacion-texto">
                                                    Lunes a Viernes: 8:00 AM - 6:00 PM<br>
                                                    Sábados: 9:00 AM - 2:00 PM<br>
                                                    Domingos: Cerrado
                                                </p>
                                            </div>
                                        </div>

                                        <a href="https://maps.app.goo.gl/1yBJZqZc9LvtSztT6"
                                           target="_blank"
                                           class="btn btn-concho w-100 mt-4">
                                            <i class="fa-solid fa-directions me-2"></i>
                                            Cómo llegar
                                        </a>
                                    </div>
                                </div>

                                {{-- MAPA --}}
                                <div class="col-12 col-lg-8">
                                    <div class="mapa-container">
                                        <iframe
                                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.7916528531428!2d-78.49400862575644!3d-0.20948473539564347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91d59a10b8c57123%3A0x7cc4dcd53937a7dd!2sPontifical%20Catholic%20University%20of%20Ecuador!5e0!3m2!1sen!2sec!4v1768664906276!5m2!1sen!2sec"
                                            width="100%"
                                            height="450"
                                            style="border:0; border-radius: 20px;"
                                            allowfullscreen=""
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade">
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SECCIÓN FAQ --}}
                    <section class="seccion-faqs">
                        <div class="text-center mb-5">
                            <h2 class="titulo-faqs">Preguntas Frecuentes</h2>
                            <p class="subtitulo-faqs">Encuentra respuestas rápidas a las consultas más comunes</p>
                        </div>

                        <div class="faq-container">
                            <div class="accordion accordion-flush" id="faqContacto">
                                @php
                                    $faqs = [
                                        ['q' => '¿Qué métodos de pago aceptan?', 'a' => 'Aceptamos tarjetas de crédito/débito Visa y Mastercard, transferencias bancarias y pagos contra entrega. Todos nuestros pagos son 100% seguros.'],
                                        ['q' => '¿Cómo realizo un pedido?', 'a' => 'Explora nuestro catálogo de productos, agrégalos al carrito y completa el proceso de compra con tus datos de entrega. Es rápido y sencillo.'],
                                        ['q' => '¿Cuál es la zona de cobertura?', 'a' => 'Realizamos entregas en toda la ciudad de Quito y valles aledaños (Cumbayá, Tumbaco, Los Chillos). Consulta disponibilidad en otras zonas.'],
                                        ['q' => '¿Puedo modificar mi pedido?', 'a' => 'Contáctanos inmediatamente vía WhatsApp o correo para verificar si tu pedido aún puede ser modificado antes del envío.'],
                                        ['q' => '¿Cuánto tiempo tarda la entrega?', 'a' => 'Las entregas se realizan en un plazo de 24 a 48 horas hábiles según tu ubicación. Para pedidos urgentes, contáctanos directamente.'],
                                        ['q' => '¿Los precios incluyen IVA?', 'a' => 'Sí, todos los precios mostrados en nuestra tienda incluyen IVA. No hay cargos ocultos ni sorpresas al finalizar tu compra.'],
                                    ];
                                @endphp

                                @foreach($faqs as $i => $f)
                                    <div class="accordion-item faq-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button faq-button @if($i > 0) collapsed @endif"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#faq{{ $i }}"
                                                    aria-expanded="@if($i === 0) true @else false @endif"
                                                    aria-controls="faq{{ $i }}">
                                                <i class="fa-solid fa-circle-question me-3"></i>
                                                {{ $f['q'] }}
                                            </button>
                                        </h2>
                                        <div id="faq{{ $i }}"
                                             class="accordion-collapse collapse @if($i === 0) show @endif"
                                             data-bs-parent="#faqContacto">
                                            <div class="accordion-body faq-body">
                                                {{ $f['a'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </section>

                </div>
                @endsection
                @section('scripts')
                    <script src="{{ asset('js/app.js') }}"></script>
@endsection
