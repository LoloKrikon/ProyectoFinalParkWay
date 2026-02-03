<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración del título de la página -->
    <?php $pageTitle = 'ParkWay - Panel Principal'; ?>
    
    <!-- Inclusión de recursos comunes (CSS, Fuentes, etc.) -->
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <section id="platform-view" class="view">
        <!-- Barra de navegación. Habilitamos perfil y logout para esta vista principal. -->
        <?php 
            $showProfile = true; 
            $showLogout = true;
            include 'includes/navbar.php'; 
        ?>

        <div class="landing-content">
            <!-- Título principal de la sección -->
            <h2 style="font-size: 2.5rem; margin-bottom: 2rem; font-weight: 600;">¿Qué necesitas hoy?</h2>

            <!-- GRID DE SELECCIÓN DE FUNCIONALIDAD -->
            <div class="platforms-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 600px; gap: 20px;">
                
                <!-- Opción 1: Buscar Aparcamiento -->
                <a href="buscar.php" class="platform-card"
                    style="text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; aspect-ratio: 1/1; transition: transform 0.2s;">
                    <div class="platform-icon" style="font-size: 4rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-magnifying-glass-location"></i>
                    </div>
                    <h3>Buscar Plaza</h3>
                </a>

                <!-- Opción 2: Liberar Aparcamiento -->
                <a href="liberar.php" class="platform-card"
                    style="text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; aspect-ratio: 1/1; transition: transform 0.2s;">
                    <div class="platform-icon" style="font-size: 4rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <h3>Liberar Plaza</h3>
                </a>

            </div>
        </div>
        
        <!-- Elemento decorativo de fondo -->
        <div class="background-ambient"></div>
    </section>

    <!-- LÓGICA DE AUTENTICACIÓN Y SESIÓN -->
    <script type="module">
        import { auth } from './js/firebase-config.js';
        import { onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

        // Comprobación de acceso: ¿Es invitado o usuario registrado?
        const isGuest = localStorage.getItem('omnilog_guest');

        // Monitor de estado de autenticación
        onAuthStateChanged(auth, (user) => {
            // Si no hay usuario y no es invitado, redirigir al login por seguridad
            if (!user && !isGuest) {
                window.location.href = 'logeo.php';
            }
        });

        // Manejador del botón de cerrar sesión
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                const confirmLogout = confirm("¿Seguro que quieres cerrar sesión?");
                if (!confirmLogout) return;

                try {
                    await signOut(auth); // Desconectar de Firebase
                } catch (error) {
                    console.error("Error al cerrar sesión:", error);
                } finally {
                    localStorage.removeItem('omnilog_guest'); // Limpiar flag local
                    window.location.href = 'logeo.php'; // Redirigir
                }
            });
        }
    </script>
</body>
</html>
