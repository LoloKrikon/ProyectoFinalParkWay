<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración del título para el login -->
    <?php $pageTitle = 'ParkWay - Iniciar Sesión'; ?>
    
    <!-- 
        Sistema de inclusión global.
        Usamos 'includes/head.php' para mantener una única fuente de verdad
        para estilos (CSS), fuentes (Google Fonts) y metaetiquetas.
    -->
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <!-- 
        Sistema de depuración visual:
        Este banner captura errores de JS que normalmente solo se verían en consola,
        útil para probar en dispositivos móviles o tabletas donde no hay DevTools fácilmente.
    -->
    <div id="error-banner" style="background: #ef4444; color: white; padding: 12px; text-align: center; display: none; font-weight: bold; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">
    </div>
    
    <script>
        // Capturador global de errores
        window.onerror = function (msg, url, line, col, error) {
            const banner = document.getElementById('error-banner');
            banner.style.display = 'block';
            banner.textContent = "Algo salió mal: " + msg + " (Línea " + line + ")";
        };
    </script>

    <section id="login-view" class="view">
        <div class="landing-content">
            <!-- Encabezado de la marca -->
            <h1 class="logo-title">ParkWay</h1>
            <p class="subtitle">Llegar tranquilo empieza por aparcar</p>

            <!-- ==========================================
                 FORMULARIO DE AUTENTICACIÓN
                 Maneja tanto Login como Registro en una misma UI
            ========================================== -->
            <div class="auth-form">
                <!-- Campos comunes -->
                <input type="email" id="email" class="auth-input" placeholder="Correo electrónico" required>
                <input type="password" id="password" class="auth-input" placeholder="Contraseña" required>

                <!-- Campos específicos de Registro (Ocultos por defecto) -->
                <div id="register-fields" style="display: none; width: 100%; text-align: center;">
                    <div style="margin: 10px 0; border-top: 1px solid rgba(255,255,255,0.1);"></div>
                    
                    <input type="text" id="nombre" class="auth-input" placeholder="Nombre completo">
                    
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                        <input type="number" id="edad" class="auth-input" placeholder="Edad">
                        <input type="tel" id="telefono" class="auth-input" placeholder="Teléfono">
                    </div>
                    
                    <input type="text" id="matricula" class="auth-input" placeholder="Matrícula del vehículo" style="text-transform: uppercase;">
                </div>

                <!-- Botón de Acción Dinámico -->
                <button id="email-action-btn" class="primary-btn">Iniciar Sesión</button>
            </div>

            <!-- Toggle Login/Registro -->
            <button id="toggle-auth-mode" class="toggle-auth">¿No tienes cuenta? Regístrate</button>

            <!-- Separador visual -->
            <div class="auth-divider" style="text-align: center; width: 100%; max-width: 320px; margin: 20px auto;">O continúa con</div>

            <!-- Opciones Alternativas -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 100%; max-width: 320px; margin: 0 auto;">
                <!-- Login con Google -->
                <button id="google-login-btn" class="google-btn">
                    <i class="fa-brands fa-google"></i> Continuar con Google
                </button>
                
                <!-- Modo Invitado (Sin persistencia) -->
                <div class="guest-container" style="width: 100%;">
                    <button id="guest-login-btn" class="secondary-btn">Acceder sin cuenta</button>
                    <!-- Tooltip informativo -->
                    <div id="guest-warning" class="auth-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        En modo invitado tus datos no se guardan al salir.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Elemento decorativo de fondo -->
        <div class="background-ambient"></div>
    </section>

    <!-- ==========================================
         LÓGICA DEL CLIENTE (Módulo JS)
    ========================================== -->
    <script type="module">
        // Importación de servicios de Firebase
        import { auth, googleProvider, db } from './js/firebase-config.js';
        import { signInWithPopup, signInWithEmailAndPassword, createUserWithEmailAndPassword, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
        import { doc, setDoc } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

        // Referencias a elementos del DOM
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        
        // Campos de registro
        const nombreInput = document.getElementById('nombre'),
              edadInput = document.getElementById('edad'),
              telefonoInput = document.getElementById('telefono'),
              matriculaInput = document.getElementById('matricula'),
              registerFields = document.getElementById('register-fields');

        // Botones
        const actionBtn = document.getElementById('email-action-btn');
        const toggleBtn = document.getElementById('toggle-auth-mode');
        const googleBtn = document.getElementById('google-login-btn');
        const guestBtn = document.getElementById('guest-login-btn');

        // Estado de la UI
        let isRegisterMode = false;     // ¿Estamos mostrando el formulario de registro?
        let isRegistering = false;       // ¿Se está procesando un registro activamente?

        // -------------------------------------------------------------
        // 1. VERIFICACIÓN DE ESTADO (Persistencia de Sesión)
        // -------------------------------------------------------------
        onAuthStateChanged(auth, (user) => {
            // Si el usuario ya está logueado y NO estamos creando una cuenta nueva,
            // redirigir automáticamente al dashboard.
            if (user && !isRegistering) {
                window.location.href = 'plataformas.php';
            }
        });

        // -------------------------------------------------------------
        // 2. GESTIÓN DE LA INTERFAZ (Toggle Login/Registro)
        // -------------------------------------------------------------
        toggleBtn.addEventListener('click', () => {
            isRegisterMode = !isRegisterMode;
            
            // Animación suave de entrada/salida (simple display none/block por compatibilidad)
            if (isRegisterMode) {
                // Modo Registro
                actionBtn.textContent = "Crear Cuenta";
                toggleBtn.textContent = "¿Ya tienes cuenta? Inicia Sesión";
                registerFields.style.display = 'block'; 
                nombreInput.focus(); // Foco amigable
            } else {
                // Modo Login
                actionBtn.textContent = "Iniciar Sesión";
                toggleBtn.textContent = "¿No tienes cuenta? Regístrate";
                registerFields.style.display = 'none'; 
                emailInput.focus();
            }
        });

        // -------------------------------------------------------------
        // 3. LÓGICA DE AUTENTICACIÓN (Email/Pass)
        // -------------------------------------------------------------
        actionBtn.addEventListener('click', async () => {
            const email = emailInput.value;
            const password = passInput.value;

            // Validación básica
            if (!email || !password) {
                alert("Por favor ingresa correo y contraseña.");
                return;
            }

            try {
                actionBtn.disabled = true; // Prevenir doble clic

                if (isRegisterMode) {
                    // === CREAR CUENTA ===
                    
                    // Validar campos obligatorios adicionales
                    if (!nombreInput.value || !edadInput.value) {
                        alert("Para registrarte necesitamos al menos tu Nombre y Edad.");
                        actionBtn.disabled = false;
                        return;
                    }

                    isRegistering = true; // Bloquea la redirección del onAuthStateChanged temporalmente
                    actionBtn.textContent = "Creando perfil...";

                    // 1. Crear usuario en Auth
                    const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                    const user = userCredential.user;

                    // 2. Preparar objeto de usuario
                    const userData = {
                        uid: user.uid,
                        email: user.email,
                        nombre: nombreInput.value,
                        edad: parseInt(edadInput.value) || 0,
                        telefono: telefonoInput.value || "",
                        matricula: matriculaInput.value ? matriculaInput.value.toUpperCase() : "",
                        vehiculo: {
                            marca: "Desconocido",
                            modelo: "",
                            color: ""
                        },
                        rol: "usuario",
                        creado: new Date().toISOString()
                    };

                    // 3. Guardar perfil extendido en Firestore
                    try {
                        await setDoc(doc(db, "users", user.uid), userData);
                        console.log("Perfil guardado correctamente.");
                    } catch (dbError) {
                        console.error("Error en Firestore:", dbError);
                        // No es crítico para bloquear el acceso, pero avisamos
                        alert("Tu cuenta se creó pero hubo un problema guardando tus datos: " + dbError.message);
                    }

                    alert("¡Bienvenido a ParkWay!");
                    window.location.href = 'plataformas.php';

                } else {
                    // === INICIAR SESIÓN ===
                    actionBtn.textContent = "Verificando...";
                    await signInWithEmailAndPassword(auth, email, password);
                    // La redirección la maneja onAuthStateChanged
                }
            } catch (error) {
                isRegistering = false;
                actionBtn.disabled = false;
                actionBtn.textContent = isRegisterMode ? "Crear Cuenta" : "Iniciar Sesión";
                
                console.error("Error Auth:", error);
                
                // Mensajes de error amigables para el usuario
                let msg = "Error desconocido.";
                switch(error.code) {
                    case 'auth/weak-password': msg = "La contraseña es muy débil (min 6 caracteres)."; break;
                    case 'auth/email-already-in-use': msg = "Este correo ya está registrado."; break;
                    case 'auth/invalid-credential': msg = "Correo o contraseña incorrectos."; break;
                    case 'auth/user-not-found': msg = "No existe una cuenta con este correo."; break;
                    case 'auth/wrong-password': msg = "Contraseña incorrecta."; break;
                }
                
                alert(msg);
            }
        });

        // -------------------------------------------------------------
        // 4. LÓGICA DE AUTENTICACIÓN (Google)
        // -------------------------------------------------------------
        // Importamos getDoc para verificar si el usuario ya existe en Firestore
        import { getDoc } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

        googleBtn.addEventListener('click', async () => {
            try {
                const result = await signInWithPopup(auth, googleProvider);
                const user = result.user;
                
                // Verificar si el usuario ya tiene un documento en Firestore
                const docRef = doc(db, "users", user.uid);
                const docSnap = await getDoc(docRef);

                if (!docSnap.exists()) {
                    // Si es la primera vez (o no tiene registro), creamos su perfil básico
                    const userData = {
                        uid: user.uid,
                        email: user.email,
                        nombre: user.displayName || "Usuario Google", // Usamos el nombre de Google si hay
                        edad: 0,
                        telefono: "",
                        matricula: "",
                        vehiculo: { marca: "", modelo: "", color: "" },
                        rol: "usuario",
                        creado: new Date().toISOString()
                    };
                    
                    await setDoc(docRef, userData);
                    console.log("Perfil de Google creado en Firestore.");
                }
                
                // Redirección manejada por onAuthStateChanged
                
            } catch (e) {
                if (e.code !== 'auth/popup-closed-by-user') {
                    console.error(e);
                    alert("No pudimos iniciar sesión con Google: " + e.message);
                }
            }
        });

        // -------------------------------------------------------------
        // 5. MODO INVITADO
        // -------------------------------------------------------------
        guestBtn.addEventListener('click', () => {
            // Marcamos un flag local para saber que estamos en modo 'Demo'
            localStorage.setItem('omnilog_guest', 'true');
            window.location.href = 'plataformas.php';
        });

        // Tooltip del botón invitado
        const warning = document.getElementById('guest-warning');
        guestBtn.addEventListener('mouseenter', () => warning.classList.add('visible'));
        guestBtn.addEventListener('mouseleave', () => warning.classList.remove('visible'));
    </script>
</body>
</html>
