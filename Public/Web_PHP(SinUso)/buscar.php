<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración para la página de búsqueda con mapa -->
    <?php 
        $pageTitle = 'ParkWay - Buscar Aparcamiento';
        
        // CSS extra para Leaflet y estilos específicos del nuevo diseño
        $extraCss = <<<EOT
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<link rel="stylesheet" href="css/map-styles.css" />
EOT;
    ?>
    <!-- Inclusión del head común -->
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <section class="view">
        <!-- 
            Barra de navegación común.
            Se adapta el header del nuevo diseño usando el componente navbar.php.
        -->
        <?php 
            $showProfile = true; 
            $backLink = 'plataformas.php';
            include 'includes/navbar.php'; 
        ?>

        <div class="map-layout">
            <!-- Panel Lateral -->
            <div class="map-panel">
                <h2>Buscar Plaza</h2>
                <div class="map-input-group">
                    <input id="searchInput" class="search-input-alt" placeholder="¿Dónde quieres ir?" />
                    <button onclick="iniciarNavegacion()" class="primary-btn" style="width: 100%; padding: 10px;">
                        <i class="fa-solid fa-location-arrow"></i> Navegar
                    </button>
                </div>

                <!-- Historial de búsquedas recientes -->
                <div id="searchHistory" style="margin-top: 20px;">
                    <h3 style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 10px;">Búsquedas recientes</h3>
                    <div id="historyList"></div>
                </div>

                <!-- Indicaciones de ruta (oculto por defecto) -->
                <div id="routeInstructions" style="display: none;">
                    <h3 style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 10px;">Indicaciones</h3>
                    <div id="instructionsList"></div>
                </div>
            </div>

            <!-- Mapa -->
            <div class="map-container" style="position: relative;">
                <div id="map"></div>
                
                <!-- Crosshair Overlay -->
                <div id="crosshair" class="center-crosshair"></div>

                <!-- Floating Controls -->
                <div class="floating-controls">
                    <button class="float-btn" onclick="localizarUsuario()">
                        <i class="fa-solid fa-crosshairs"></i> Localizar
                    </button>
                    <button id="btn-moverme" class="float-btn" onclick="toggleMoverme()">
                        <i class="fa-solid fa-up-down-left-right"></i> Moverme
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Leaflet Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    
    <!-- Lógica del Mapa (Extraída a archivo externo) -->
    <script type="module" src="js/buscar-map.js"></script>
</body>
</html>
