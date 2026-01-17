<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Ecommerce Híbrido</title>
    <!-- Tailwind CSS para que se vea decente rápido -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FIREBASE SDKs -->
    <script type="module">
        // Importar las funciones necesarias de Firebase
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getDatabase, ref, set, push, onValue, remove, get }
            from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

        // --- TU CONFIGURACIÓN DE FIREBASE (PEGA TUS DATOS AQUÍ) ---
        const firebaseConfig = {
            apiKey: "TU_API_KEY",
            authDomain: "TU_PROYECTO.firebaseapp.com",
            databaseURL: "https://TU_PROYECTO-default-rtdb.firebaseio.com",
            projectId: "TU_PROYECTO",
            storageBucket: "TU_PROYECTO.appspot.com",
            messagingSenderId: "...",
            appId: "..."
        };

        // Inicializar Firebase
        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        // Variables Globales
        let currentUser = JSON.parse(localStorage.getItem('user_data')) || null;
        let authToken = localStorage.getItem('auth_token') || null;

        // --- LÓGICA DE INTERFAZ ---

        window.onload = function() {
            if(currentUser) {
                mostrarTienda();
                escucharCarrito();
            } else {
                mostrarLogin();
            }
        };

        // 1. FUNCIONES DE AUTENTICACIÓN (API LARAVEL)

        window.login = async function() {
            const email = document.getElementById('log_email').value;
            const password = document.getElementById('log_pass').value;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();

                if(response.ok) {
                    guardarSesion(data);
                } else {
                    alert(data.message);
                }
            } catch (error) { console.error(error); }
        };

        window.register = async function() {
            const form = document.getElementById('registerForm');
            const formData = new FormData(form);
            const dataObj = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dataObj)
                });
                const data = await response.json();

                if(response.ok) {
                    guardarSesion(data);
                } else {
                    alert('Error: ' + JSON.stringify(data));
                }
            } catch (error) { console.error(error); }
        };

        function guardarSesion(data) {
            localStorage.setItem('auth_token', data.access_token);
            // Guardamos user_id y cliente_id para identificar el carrito en Firebase
            // Nota: Ajusta esto según lo que devuelve tu API (data.user o data.data)
            const userData = data.user || data.data;
            localStorage.setItem('user_data', JSON.stringify(userData));

            currentUser = userData;
            authToken = data.access_token;

            mostrarTienda();
            escucharCarrito();
        }

        window.logout = function() {
            localStorage.clear();
            location.reload();
        }

        // 2. LÓGICA DEL CARRITO (FIREBASE NoSQL)

        // Estructura en Firebase: carts/CLIENTE_ID/items/PRODUCTO_ID

        window.agregarAlCarrito = function(idProducto, nombre, precio) {
            if(!currentUser) return alert("Inicia sesión primero");

            const cartPath = `carts/${currentUser.id_cliente}/items/${idProducto}`;
            const itemRef = ref(db, cartPath);

            // Primero leemos si ya existe para sumar cantidad, o lo creamos
            get(itemRef).then((snapshot) => {
                if (snapshot.exists()) {
                    const currentData = snapshot.val();
                    set(itemRef, {
                        ...currentData,
                        cantidad: currentData.cantidad + 1,
                        subtotal: (currentData.cantidad + 1) * precio
                    });
                } else {
                    set(itemRef, {
                        id_producto: idProducto,
                        nombre: nombre,
                        precio: precio,
                        cantidad: 1,
                        subtotal: precio
                    });
                }
                alert("Producto agregado a Firebase");
            });
        };

        function escucharCarrito() {
            const cartPath = `carts/${currentUser.id_cliente}/items`;
            const cartRef = ref(db, cartPath);

            // "onValue" escucha cambios en tiempo real
            onValue(cartRef, (snapshot) => {
                const cartContainer = document.getElementById('cart-items');
                cartContainer.innerHTML = '';
                let totalGeneral = 0;
                let cartData = [];

                snapshot.forEach((childSnapshot) => {
                    const item = childSnapshot.val();
                    const key = childSnapshot.key;
                    cartData.push(item);
                    totalGeneral += item.subtotal;

                    cartContainer.innerHTML += `
                        <div class="flex justify-between border-b py-2">
                            <span>${item.nombre} (x${item.cantidad})</span>
                            <span>$${item.subtotal}</span>
                            <button onclick="eliminarDelCarrito('${key}')" class="text-red-500 text-sm">X</button>
                        </div>
                    `;
                });

                document.getElementById('cart-total').innerText = totalGeneral;

                // Guardamos los datos del carrito en una variable global para enviarlos al pagar
                window.datosCarritoActual = cartData;
            });
        }

        window.eliminarDelCarrito = function(idProducto) {
            const itemRef = ref(db, `carts/${currentUser.id_cliente}/items/${idProducto}`);
            remove(itemRef);
        };

        // 3. PAGAR (CHECKOUT -> SQL)

        window.pagar = async function() {
            if(!window.datosCarritoActual || window.datosCarritoActual.length === 0) return alert("Carrito vacío");

            if(!confirm("¿Confirmar compra y generar factura?")) return;

            try {
                const response = await fetch('/api/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}` // Enviamos el Token Sanctum
                    },
                    body: JSON.stringify({
                        items: window.datosCarritoActual
                    })
                });

                const data = await response.json();

                if(response.ok) {
                    alert("¡Compra exitosa! Factura #" + data.factura_id);
                    // Limpiamos Firebase
                    remove(ref(db, `carts/${currentUser.id_cliente}`));
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) { console.error(error); }
        };

        // UI HELPERS
        function mostrarLogin() {
            document.getElementById('auth-section').classList.remove('hidden');
            document.getElementById('shop-section').classList.add('hidden');
        }
        function mostrarTienda() {
            document.getElementById('auth-section').classList.add('hidden');
            document.getElementById('shop-section').classList.remove('hidden');
            document.getElementById('user-name-display').innerText = currentUser.nombre;
        }
    </script>
</head>
<body class="bg-gray-100 p-5">

<!-- SECCIÓN DE AUTENTICACIÓN -->
<div id="auth-section" class="max-w-md mx-auto bg-white p-6 rounded shadow hidden">
    <h2 class="text-xl font-bold mb-4">Acceso Clientes</h2>

    <!-- Login -->
    <div class="mb-6">
        <h3 class="font-bold text-gray-600">Iniciar Sesión</h3>
        <input type="email" id="log_email" placeholder="Email" class="border p-2 w-full mb-2">
        <input type="password" id="log_pass" placeholder="Contraseña" class="border p-2 w-full mb-2">
        <button onclick="login()" class="bg-blue-500 text-white p-2 w-full rounded">Entrar</button>
    </div>

    <hr class="my-4">

    <!-- Registro -->
    <div>
        <h3 class="font-bold text-gray-600">Registrarse (Si no tienes cuenta)</h3>
        <form id="registerForm" onsubmit="event.preventDefault(); register();">
            <input type="text" name="identificacion" placeholder="Cédula / RUC (Obligatorio)" class="border p-2 w-full mb-2" required>
            <input type="text" name="nombre" placeholder="Nombre Completo" class="border p-2 w-full mb-2" required>
            <input type="email" name="email" placeholder="Email" class="border p-2 w-full mb-2" required>
            <input type="password" name="password" placeholder="Contraseña" class="border p-2 w-full mb-2" required>
            <input type="text" name="direccion" placeholder="Dirección" class="border p-2 w-full mb-2">
            <button type="submit" class="bg-green-500 text-white p-2 w-full rounded">Registrarme</button>
        </form>
    </div>
</div>

<!-- SECCIÓN DE TIENDA -->
<div id="shop-section" class="hidden">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Bienvenido, <span id="user-name-display"></span></h1>
        <button onclick="logout()" class="text-red-500 underline">Cerrar Sesión</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- LISTA DE PRODUCTOS (Esto deberías generarlo con un @foreach de Laravel) -->
        <div class="md:col-span-2">
            <h2 class="text-xl font-bold mb-4">Productos</h2>
            <div class="grid grid-cols-2 gap-4">
                <!-- Producto 1 (Ejemplo estático, hazlo dinámico con Blade) -->
                <div class="bg-white p-4 shadow rounded">
                    <h3 class="font-bold">Laptop Gamer</h3>
                    <p class="text-gray-500">$1200.00</p>
                    <button onclick="agregarAlCarrito(1, 'Laptop Gamer', 1200)" class="mt-2 bg-blue-600 text-white px-4 py-1 rounded w-full">
                        Agregar
                    </button>
                </div>

                <!-- Producto 2 -->
                <div class="bg-white p-4 shadow rounded">
                    <h3 class="font-bold">Mouse Inalámbrico</h3>
                    <p class="text-gray-500">$25.00</p>
                    <button onclick="agregarAlCarrito(2, 'Mouse Inalámbrico', 25)" class="mt-2 bg-blue-600 text-white px-4 py-1 rounded w-full">
                        Agregar
                    </button>
                </div>
            </div>
        </div>

        <!-- CARRITO DE COMPRAS (FIREBASE) -->
        <div class="bg-white p-4 shadow rounded h-fit">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Tu Carrito</h2>

            <div id="cart-items" class="mb-4 text-sm text-gray-700">
                <!-- Aquí se inyecta el HTML desde JS -->
                <p class="text-gray-400 italic">Cargando carrito...</p>
            </div>

            <div class="flex justify-between font-bold text-lg mb-4">
                <span>Total:</span>
                <span>$<span id="cart-total">0</span></span>
            </div>

            <button onclick="pagar()" class="bg-green-600 text-white p-3 rounded w-full font-bold hover:bg-green-700 transition">
                PAGAR AHORA (SQL)
            </button>
        </div>
    </div>
</div>

</body>
</html>
