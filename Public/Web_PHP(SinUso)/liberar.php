<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración para la página de liberar plaza -->
    <?php 
        $pageTitle = 'ParkWay - Dejar Sitio Libre';
        
        // Leaflet CSS (Mapas)
        $extraCss = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                     <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />';
    ?>
    <?php include 'includes/head.php'; ?>

    <style>
        /* Ajuste de margen para el feedback (Mantenido aquí por ser mínimo) */
        .feedback-message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.95rem;
            text-align: center;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.5s ease;
        }

        .feedback-message.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <section class="view">
        <!-- Barra de navegación común -->
        <?php 
            $showProfile = true; 
            $backLink = 'plataformas.php'; // Volver al menú principal
            include 'includes/navbar.php'; 
        ?>

        <div class="map-layout">
            <!-- PANEL LATERAL: Confirmación de acción -->
            <div class="map-panel">
                <h2>🅿️ Liberar Plaza</h2>
                
                <div class="map-input-group">
                    <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5;">
                        ¿Vas a salir? Confirma tu ubicación para avisar a otros conductores y ayudar a la comunidad ParkWay.
                    </p>
                    
                    <!-- Botón de Acción Principal: Liberar -->
                    <button id="btn-liberar" onclick="liberarPlaza()" class="primary-btn"
                        style="width: 100%; padding: 14px; background: linear-gradient(135deg, #10b981, #059669); font-weight: bold; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); margin-bottom: 15px;">
                        <i class="fa-solid fa-share-from-square"></i> Liberar mi sitio aquí
                    </button>
                    
                    <!-- Área de mensajes de estado (éxito/error) -->
                    <div id="statusMessage" class="feedback-message"></div>
                </div>

                <!-- Info Box -->
                <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 12px; font-size: 0.85rem; color: #aaa;">
                    <i class="fa-solid fa-circle-info"></i> 
                    Al liberar tu plaza ganas puntos de comunidad. ¡Gracias por colaborar!
                </div>
            </div>

            <!-- CONTENEDOR DEL MAPA -->
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </section>

    <!-- SCRIPTS LEAFLET -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>

    <!-- LÓGICA JAVASCRIPT (Extraída) -->
    <script type="module" src="js/liberar-map.js"></script>
</body>
</html>
