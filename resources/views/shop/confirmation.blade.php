@extends('layouts.app')

@section('titulo', 'Pedido Confirmado')

@section('contenido')
    <div class="container mt-5 mb-5">

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden mx-auto" style="max-width: 800px;">
            {{-- CABECERA VERDE --}}
            <div class="bg-success text-white text-center p-5">
                <div class="mb-3">
                    <i class="fa-regular fa-circle-check fa-5x animate-bounce"></i>
                </div>
                <h1 class="fw-bold">¡Gracias por tu compra!</h1>
                <p class="fs-5">Tu pedido ha sido recibido correctamente.</p>
                <div class="mt-3 badge bg-white text-success fs-6 px-4 py-2 rounded-pill">
                    Orden #{{ $orderId }}
                </div>
            </div>

            <div class="card-body p-5">

                <h4 class="fw-bold text-center mb-4">Estado del Pedido en Tiempo Real</h4>

                {{-- LÍNEA DE TIEMPO (STEPPER) --}}
                <div class="position-relative m-4">
                    <div class="progress" style="height: 4px;">
                        <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                    </div>

                    <div class="d-flex justify-content-between position-relative" style="top: -14px;">
                        {{-- PASO 1 --}}
                        <div class="text-center step-item" id="step-recibido">
                            <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 30px; height: 30px;">
                                <i class="fa-solid fa-clipboard-check small"></i>
                            </div>
                            <small class="fw-bold mt-2 d-block text-muted">Recibido</small>
                        </div>

                        {{-- PASO 2 --}}
                        <div class="text-center step-item" id="step-preparando">
                            <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 30px; height: 30px;">
                                <i class="fa-solid fa-box-open small"></i>
                            </div>
                            <small class="fw-bold mt-2 d-block text-muted">Preparando</small>
                        </div>

                        {{-- PASO 3 --}}
                        <div class="text-center step-item" id="step-camino">
                            <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 30px; height: 30px;">
                                <i class="fa-solid fa-truck-fast small"></i>
                            </div>
                            <small class="fw-bold mt-2 d-block text-muted">En Camino</small>
                        </div>

                        {{-- PASO 4 --}}
                        <div class="text-center step-item" id="step-entregado">
                            <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 30px; height: 30px;">
                                <i class="fa-solid fa-house-chimney small"></i>
                            </div>
                            <small class="fw-bold mt-2 d-block text-muted">Entregado</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light border text-center mt-5">
                    <p class="mb-1 text-muted">¿Necesitas ayuda?</p>
                    <strong><i class="fa-brands fa-whatsapp text-success"></i> +593 99 999 9999</strong>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-2"></i> Volver a la Tienda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Animación del check */
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
        .animate-bounce { animation: bounceIn 0.8s ease-out; }

        /* Estilos activos del stepper */
        .step-active .step-circle { background-color: #198754 !important; transform: scale(1.2); transition: 0.3s; }
        .step-active small { color: #198754 !important; }
    </style>

    {{-- SCRIPT FIREBASE --}}
    <script type="module">
        // Importamos Firebase (Usamos la versión modular que pediste)
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js";
        import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-database.js";

        // TU CONFIGURACIÓN (Asegúrate que sea la misma de tu proyecto)
        const firebaseConfig = {
            databaseURL: "https://marketcuy-68a16-default-rtdb.firebaseio.com"
        };

        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        // Escuchamos cambios en: orders/FCTxxxx
        const orderId = "{{ $orderId }}";
        const orderRef = ref(db, 'orders/' + orderId);

        onValue(orderRef, (snapshot) => {
            const data = snapshot.val();

            if (data && data.estado_envio) {
                updateTimeline(data.estado_envio);
            } else {
                // Si no hay estado (recién creado), asumimos pendiente
                updateTimeline('PENDIENTE');
            }
        });

        function updateTimeline(estado) {
            // Limpiar clases
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('step-active'));
            const bar = document.getElementById('progress-bar');

            // Normalizar estado (mayúsculas)
            const st = estado.toUpperCase();

            // Lógica de progreso
            let progress = 0;

            if (st === 'PENDIENTE' || st === 'RECIBIDO') {
                document.getElementById('step-recibido').classList.add('step-active');
                progress = 0;
            }
            else if (st === 'PREPARANDO') {
                document.getElementById('step-recibido').classList.add('step-active');
                document.getElementById('step-preparando').classList.add('step-active');
                progress = 33;
            }
            else if (st === 'CAMINO' || st === 'EN CAMINO') {
                document.getElementById('step-recibido').classList.add('step-active');
                document.getElementById('step-preparando').classList.add('step-active');
                document.getElementById('step-camino').classList.add('step-active');
                progress = 66;
            }
            else if (st === 'ENTREGADO') {
                document.getElementById('step-recibido').classList.add('step-active');
                document.getElementById('step-preparando').classList.add('step-active');
                document.getElementById('step-camino').classList.add('step-active');
                document.getElementById('step-entregado').classList.add('step-active');
                progress = 100;
            }

            // Animar barra
            bar.style.width = progress + '%';
        }
    </script>
@endsection
